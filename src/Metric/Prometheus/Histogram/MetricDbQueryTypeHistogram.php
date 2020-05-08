<?php

namespace App\Metric\Prometheus\Histogram;

use App\Metric\DTO\SqlQueryDTO;
use App\Metric\Transformer\MetricDebugStackTransformer;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;

class MetricDbQueryTypeHistogram
{
    private CollectorRegistry $collectorRegistry;

    private MetricDebugStackTransformer $metricDebugStackTransformer;

    public function __construct(
        CollectorRegistry $collectorRegistry,
        MetricDebugStackTransformer $metricDebugStackTransformer
    ) {
        $this->collectorRegistry = $collectorRegistry;
        $this->metricDebugStackTransformer = $metricDebugStackTransformer;
    }

    /**
     * @param array  $queryList
     * @param string $metricName
     * @param array  $labelList
     *
     * @throws PrometheusException
     */
    public function observe(array $queryList, string $metricName, array $labelList = []): void
    {
        $histogram = $this->collectorRegistry->getHistogram($metricName);
        foreach ($queryList as $query) {
            $result = $this->metricDebugStackTransformer->getInfoAboutSql($query);
            if ($result instanceof SqlQueryDTO) {
                $histogram->observe(1, $this->getQueryTypeLabels($labelList, $result->getType(), $result->getTableName()));
            }
        }
    }

    private function getQueryTypeLabels($labelList, string $type, string $table): array
    {
        return array_merge($labelList, [$type, $table]);
    }
}
