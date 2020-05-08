<?php

namespace App\Storage\Redis;

/*
 * This is simplified version usually we need additions actions like set, get, etc.
 */
interface RedisStorageInterface
{
    /**
     * For easier data management we want to have prefix for redis keys
     * Set it here
     *
     * @param string $key
     * @param bool   $addSeparator
     */
    public function setPrefix(string $key, bool $addSeparator = true): void;

    /**
     * Clear data which has specified prefix
     */
    public function clear(): void;
}
