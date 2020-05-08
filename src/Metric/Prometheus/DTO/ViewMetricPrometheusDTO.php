<?php

namespace App\Metric\Prometheus\DTO;

class ViewMetricPrometheusDTO
{
    private string $namespace;

    private string $name;

    private string $description;

    private string $data;

    private string $type;

    private array $labels = [];

    public function __construct(string $namespace, string $name, string $description, string $type, string $data)
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->description = $description;
        $this->data = $data;
        $this->type = $type;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }
}
