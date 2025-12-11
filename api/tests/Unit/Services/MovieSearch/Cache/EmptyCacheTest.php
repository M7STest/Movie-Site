<?php

namespace Tests\Unit\Services\MovieSearch\Cache;

use App\Services\MovieSearch\Cache\EmptyCache;
use App\Services\MovieSearch\Factories\EmptyCacheFactory;
use Tests\TestCase;

class EmptyCacheTest extends TestCase {

    public function test_get_data_returns_empty_array(): void {
        $emptyCache = new EmptyCache();
        
        $this->assertEquals([], $emptyCache->getData());
    }

    public function test_set_data_does_nothing(): void {
        $emptyCache = new EmptyCache();
        
        $emptyCache->setData(['some' => 'data', 'test' => 'value']);
        
        $this->assertEquals([], $emptyCache->getData());
    }

    public function test_set_and_get_cache_key(): void {
        $emptyCache = new EmptyCache();
        
        $result = $emptyCache->setKey('test_key_123');
        
        $this->assertSame($emptyCache, $result);
        $this->assertEquals('test_key_123', $emptyCache->getCacheKey());
    }

    public function test_get_full_cache_key_with_prefix(): void {
        $emptyCache = new EmptyCache();
        $emptyCache->setKey('movie_not_found');
        
        $this->assertEquals('empty_cache_movie_not_found', $emptyCache->getFullCacheKey());
    }

    public function test_build_cache_key_statically(): void {
        $cacheKey = EmptyCache::buildCacheKey('static_key');
        
        $this->assertEquals('empty_cache_static_key', $cacheKey);
    }

    public function test_get_factory_class(): void {
        $factoryClass = EmptyCache::getFactoryClass();
        
        $this->assertEquals(EmptyCacheFactory::class, $factoryClass);
    }

    public function test_default_ttl(): void {
        $emptyCache = new EmptyCache();
        
        $this->assertEquals(3600, $emptyCache->getTtl());
    }

    public function test_fluent_setter_chaining(): void {
        $emptyCache = new EmptyCache();
        
        $result = $emptyCache
            ->setKey('first_key')
            ->setKey('second_key');
        
        $this->assertSame($emptyCache, $result);
        $this->assertEquals('second_key', $emptyCache->getCacheKey());
    }
}
