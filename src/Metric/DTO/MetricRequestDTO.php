<?php

namespace App\Metric\DTO;

class MetricRequestDTO
{
    private string $controllerPath = '';

    private string $controllerAction = '';

    private string $routingName = '';

    private string $urlAction = '';

    private array $requestData = [];

    public function __construct(
        string $routingName,
        string $controllerPath,
        string $controllerAction,
        array $requestData,
        string $urlAction
    ) {
        $this->routingName = $routingName;
        $this->controllerPath = $controllerPath;
        $this->controllerAction = $controllerAction;
        $this->requestData = $requestData;
        $this->urlAction = $urlAction;
    }

    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }

    public function getControllerAction(): string
    {
        return $this->controllerAction;
    }

    public function getRoutingName(): string
    {
        return $this->routingName;
    }

    public function getUrlAction(): string
    {
        return $this->urlAction;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }
}

