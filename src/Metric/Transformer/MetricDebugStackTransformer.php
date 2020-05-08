<?php

namespace App\Metric\Transformer;

use Doctrine\DBAL\Logging\DebugStack;
use App\Metric\DTO\SqlQueryDTO;

class MetricDebugStackTransformer
{
    public function getQueryExecutionTime(DebugStack $debugStack): float
    {
        $time = 0;
        foreach ($debugStack->queries as $query) {
            if (isset($query['executionMS']) && is_numeric($query['executionMS'])) {
                $time += $query['executionMS'];
            }
        }

        return $time;
    }

    public function getInfoAboutSql(array $query): ?SqlQueryDTO
    {
        if (array_key_exists('sql', $query) && preg_match('/(DELETE FROM|UPDATE|INSERT INTO) ([a-zA-Z0-9_]+)/', $query['sql'], $result)) {
            return new SqlQueryDTO($result[1], $result[2]);
        }

        return null;
    }
}
