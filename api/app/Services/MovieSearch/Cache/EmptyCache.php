<?php 

namespace App\Services\MovieSearch\Cache;

use App\Services\MovieSearch\Factories\EmptyCacheFactory;

class EmptyCache extends CacheableModel {
    
    protected static string $cacheKeyPrefix = "empty_cache";
    protected int $ttl = 3600;
    protected string $key = "";

    public function setKey(string $key): self {
        $this->key = $key;
        return $this;
    }

    public function getData(): array {
        return [];
    }

    public function setData(array $data): void {
        return;
    }

    public function getCacheKey(): string {
        return $this->key;
    }

    public static function getFactoryClass() {
        return EmptyCacheFactory::class;
    }

}