<?php

namespace App\Services\MovieSearch\Cache;

abstract class CacheableModel {
    
    protected static string $cacheKeyPrefix = '';
    protected int $ttl = 3600;

    abstract public function getData(): array;
    
    abstract public function setData(array $data): void;
    
    abstract public function getCacheKey(): string;

    abstract public static function getFactoryClass();

    public function getTtl(): int {
        return $this->ttl;
    }

    public function getFullCacheKey(): string {
        return static::buildCacheKey($this->getCacheKey());
    }

    public static function buildCacheKey(string $key): string {
        return static::$cacheKeyPrefix . '_' . $key;
    }

}