<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\ControllerActionMetricController;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use App\Metric\DTO\MetricRequestDTO;
use App\Metric\Enum\LogMetricNameEnum;
use App\Metric\Prometheus\Histogram\MetricDbQueryTypeHistogram;
use App\Metric\Transformer\MetricDebugStackTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;

class LogControllerActionSubscriber implements EventSubscriberInterface
{
    const APP_NAME = 'example_app';

    /**
     * Fields to count how long endpoint works
     *
     * @var float
     */
    private float $executionTimeStart = 0;

    private DebugStack $debugStack;

    private ManagerRegistry $registry;

    private CollectorRegistry $collectorRegistry;

    private RequestStack $requestStack;

    private MetricDebugStackTransformer $metricDebugStackTransformer;

    /**
     * Additional data required to measure
     *
     * @var MetricRequestDTO|null
     */
    private ?MetricRequestDTO $metricRequestDTO = null;

    /**
     * Service to measure db query types like INSERT, UPDATE, DELETE
     *
     * @var MetricDbQueryTypeHistogram
     */
    private MetricDbQueryTypeHistogram $metricDbQueryTypeHistogram;

    /**
     * Useful flag to know which environment is used
     *
     * @var string
     */
    private string $envForPrometheus;

    private LoggerInterface $logger;

    /**
     * Flag to disable metrics
     *
     * @var bool
     */
    private bool $isSubscriberEnabled = false;

    /**
     * We don't want to log some actions
     */
    private array $excludedList = [
        'getLogAction' => [
            ControllerActionMetricController::class,
        ],
    ];

    public function __construct(
        ManagerRegistry $registry,
        CollectorRegistry $collectorRegistry,
        RequestStack $requestStack,
        LoggerInterface $logger,
        MetricDbQueryTypeHistogram $metricDbQueryTypeHistogram,
        MetricDebugStackTransformer $metricDebugStackTransformer,
        string $envForPrometheus,
        bool $isSubscriberEnabled = false
    ) {
        $this->logger = $logger;
        $this->registry = $registry;
        $this->collectorRegistry = $collectorRegistry;
        $this->requestStack = $requestStack;
        $this->metricDebugStackTransformer = $metricDebugStackTransformer;
        $this->metricDbQueryTypeHistogram = $metricDbQueryTypeHistogram;
        $this->isSubscriberEnabled = $isSubscriberEnabled;
        $this->envForPrometheus = $envForPrometheus;
    }

    /**
     * We need to start measure data just before a controller is called
     *
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        // Sometimes we need to disable logs, we use special flag for that
        if (!$this->isSubscriberEnabled) {
            return;
        }

        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (false === is_array($controller)) {
            $this->logger->error('Expected controller action');

            return;
        }

        $controllerPath = \get_class($controller[0]);
        // check blacklist, usually we don't want to measure some endpoints like endpoint to get metric data
        if ($this->isExcluded($controllerPath, $controller[1])) {
            return;
        }

        // measure only "real" routing
        $routingName = $this->requestStack->getCurrentRequest()->get('_route');
        if (empty($routingName)) {
            return;
        }

        $this->metricRequestDTO = new MetricRequestDTO($routingName, $controllerPath, $controller[1], $this->requestStack->getCurrentRequest()->request->all(), $this->requestStack->getCurrentRequest()->getPathInfo());
        // required to measure database queries
        $this->debugStack = new DebugStack();
        $this->registry->getConnection()->getConfiguration()->setSQLLogger($this->debugStack);
        $this->executionTimeStart = \microtime(true);
    }

    /**
     * We need to measue just after a controller was called
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        //in some cases onKernelController is not called
        if (!$this->metricRequestDTO instanceof MetricRequestDTO) {
            return;
        }

        try {
            $this->executeCounter();
            $this->executeHistogram();
        } catch (\Throwable $e) {
            $this->logger->critical(sprintf('Not expected problem with Prometheus logs: %s', $e->getMessage()), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 255],
            KernelEvents::RESPONSE => ['onKernelResponse', -255],
        ];
    }

    private function isExcluded(string $controllerPath, string $method): bool
    {
        return (isset($this->excludedList[$method]) && in_array($controllerPath, $this->excludedList[$method]));
    }

    /**
     * @throws PrometheusException
     */
    private function executeCounter(): void
    {
        //We have to now if we use sub requests
        if ($this->isSubRequest()) {
            $counter = $this->collectorRegistry->getCounter(LogMetricNameEnum::APP_SUB_REQUEST_CALLED_QUANTITY_TOTAL);
            $counter->inc(1, $this->getLabels());

            return;
        }

        //log it only for master request
        $counter = $this->collectorRegistry->getCounter(LogMetricNameEnum::APP_REQUEST_CALLED_QUANTITY_TOTAL);
        $counter->inc(1, $this->getLabels());
    }

    /**
     * @throws PrometheusException
     */
    private function executeHistogram(): void
    {
        //log it only for master request
        if ($this->isSubRequest()) {
            return;
        }

        $histogram = $this->collectorRegistry->getHistogram(LogMetricNameEnum::APP_REQUEST_EXECUTION_TIME_SECONDS);
        $histogram->observe(\round(microtime(true) - $this->executionTimeStart, 2), $this->getLabels());

        $histogram = $this->collectorRegistry->getHistogram(LogMetricNameEnum::APP_REQUEST_DB_QUERY_QUANTITY);
        $histogram->observe(\count($this->debugStack->queries), $this->getLabels());

        $histogram = $this->collectorRegistry->getHistogram(LogMetricNameEnum::APP_REQUEST_DB_QUERY_EXECUTION_TIME_SECONDS);
        $histogram->observe(\round($this->metricDebugStackTransformer->getQueryExecutionTime($this->debugStack), 2), $this->getLabels());

        $this->metricDbQueryTypeHistogram->observe($this->debugStack->queries, LogMetricNameEnum::APP_REQUEST_DB_QUERY_TYPE, $this->getLabels());
    }

    private function isSubRequest(): bool
    {
        return $this->requestStack->getMasterRequest() && ($this->requestStack->getCurrentRequest() !== $this->requestStack->getMasterRequest());
    }

    private function getLabels(): array
    {
        return [
            $this->metricRequestDTO->getRoutingName(),
            $this->envForPrometheus,
            self::APP_NAME,
        ];
    }
}
