<?php

namespace Tests\Unit\Services\MovieSearch\Cache;

use App\Services\MovieSearch\Cache\CacheableModel;
use Tests\TestCase;

class TestCacheableModel extends CacheableModel {
    protected static string $cacheKeyPrefix = 'test';
    protected int $ttl = 3600;
    private string $cacheKey = '';
    private array $data = [];

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data): void {
        $this->data = $data;
    }

    public function getCacheKey(): string {
        return $this->cacheKey;
    }

    public function setCacheKey(string $key): void {
        $this->cacheKey = $key;
    }

    public static function getFactoryClass() {
        return 'TestFactory';
    }

    public function setTtl(int $ttl): void {
        $this->ttl = $ttl;
    }
}

class CacheableModelTest extends TestCase {

    public function test_returns_correct_ttl(): void {
        $model = new TestCacheableModel();
        
        $this->assertEquals(3600, $model->getTtl());
        
        $model->setTtl(7200);
        $this->assertEquals(7200, $model->getTtl());
    }

    public function test_returns_correct_full_cache_key(): void {
        $model = new TestCacheableModel();
        $model->setCacheKey('movie123');
        
        $this->assertEquals('test_movie123', $model->getFullCacheKey());
    }

    public function test_builds_cache_key_statically(): void {
        $cacheKey = TestCacheableModel::buildCacheKey('item456');
        
        $this->assertEquals('test_item456', $cacheKey);
    }

    public function test_get_and_set_data(): void {
        $model = new TestCacheableModel();
        $testData = ['title' => 'Test Movie', 'year' => '2024'];
        
        $model->setData($testData);
        
        $this->assertEquals($testData, $model->getData());
    }

    public function test_get_factory_class(): void {
        $factoryClass = TestCacheableModel::getFactoryClass();
        
        $this->assertEquals('TestFactory', $factoryClass);
    }
}
