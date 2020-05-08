<?php

declare(strict_types=1);

namespace App\Metric\DTO;

class SqlQueryDTO
{
    private string $type = '';

    private string $tableName = '';

    public function __construct(string $type, string $tableName)
    {
        $this->type = $type;
        $this->tableName = $tableName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}

