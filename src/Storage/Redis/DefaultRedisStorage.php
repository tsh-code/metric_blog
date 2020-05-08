<?php

namespace App\Storage\Redis;

use Predis\ClientInterface;

/**
 * Custom class for redis to easier data management
 */
class DefaultRedisStorage implements RedisStorageInterface
{
    private ClientInterface $client;

    private string $prefix;

    private string $defaultPrefix;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->defaultPrefix = '_';
    }

    public function setPrefix(string $key, bool $addSeparator = true): void
    {
        $this->prefix = $key;
        if ($addSeparator) {
            $this->prefix .= '_';
        }
    }

    public function clear(): void
    {
        $keyList = $this->client->keys($this->getPreparedKey('*'));

        if (false === empty($keyList)) {
            $this->client->del($keyList);
        }
    }

    private function getPreparedKey(string $key): string
    {
        return $this->getPrefix().$key;
    }

    private function getPrefix(): string
    {
        if ($this->prefix) {
            return $this->prefix;
        }

        return $this->prefix = $this->defaultPrefix;
    }
}
