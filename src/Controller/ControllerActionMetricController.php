<?php

declare(strict_types=1);

namespace App\Controller;

use App\Storage\Redis\RedisStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\Format\TextFormatter;
use Symfony\Component\Routing\Annotation\Route;

class ControllerActionMetricController extends AbstractController
{
    private RedisStorageInterface $redisStorage;

    private CollectorRegistry $collectorRegistry;

    public function __construct(RedisStorageInterface $redisStorage, CollectorRegistry $collectorRegistry)
    {
        $this->redisStorage = $redisStorage;
        $this->collectorRegistry = $collectorRegistry;
    }

    /**
     * @Route("/metrics", name="api_get_metrics", methods={"GET"})
     * @return Response
     */
    public function getLogAction(): Response
    {
        $formatter = new TextFormatter();

        $data = $formatter->format($this->collectorRegistry->collect());

        //clear storage when data was taken
        $this->redisStorage->clear();

        return new Response(
            $data,
            Response::HTTP_OK, [
            'Content-Type' => $formatter->getMimeType(),
        ]);
    }
}
