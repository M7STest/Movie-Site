<?php

namespace Tests\Unit\Services\MovieSearch\Cache;

use App\Services\MovieSearch\Cache\CacheableModel;
use App\Services\MovieSearch\Cache\CacheManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

// Test model implementation
class TestCacheableModelForManager extends CacheableModel {
    protected static string $cacheKeyPrefix = 'test';
    protected int $ttl = 3600;
    private string $cacheKey = 'key123';
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
        return TestFactoryForManager::class;
    }

    public function setTtl(int $ttl): void {
        $this->ttl = $ttl;
    }
}

// Test factory
class TestFactoryForManager {
    public function make(): TestCacheableModelForManager {
        return new TestCacheableModelForManager();
    }
}

class CacheManagerTest extends TestCase {

    private CacheManager $cacheManager;

    protected function setUp(): void {
        parent::setUp();
        $this->cacheManager = new CacheManager();
        Redis::shouldReceive('connection')->andReturnSelf();
    }

    public function test_store_saves_model_to_redis(): void {
        $model = new TestCacheableModelForManager();
        $model->setCacheKey('movie123');
        $model->setData(['title' => 'Test Movie', 'year' => '2024']);
        $model->setTtl(7200);

        $expectedKey = 'test_movie123';
        $expectedData = json_encode([
            CacheManager::DATA_KEY => ['title' => 'Test Movie', 'year' => '2024'],
            CacheManager::FACTORY_KEY => TestFactoryForManager::class,
        ]);

        Redis::shouldReceive('set')
            ->once()
            ->with($expectedKey, $expectedData, 'EX', 7200);

        $this->cacheManager->store($model);
    }

    public function test_get_retrieves_and_reconstructs_model(): void {
        $key = 'test_movie123';
        $storedData = json_encode([
            CacheManager::DATA_KEY => ['title' => 'Cached Movie', 'year' => '2023'],
            CacheManager::FACTORY_KEY => TestFactoryForManager::class,
        ]);

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($storedData);

        App::shouldReceive('make')
            ->once()
            ->with(TestFactoryForManager::class)
            ->andReturn(new TestFactoryForManager());

        $result = $this->cacheManager->get($key);

        $this->assertInstanceOf(TestCacheableModelForManager::class, $result);
        $this->assertEquals(['title' => 'Cached Movie', 'year' => '2023'], $result->getData());
    }

    public function test_get_returns_null_when_key_not_found(): void {
        $key = 'nonexistent_key';

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn(null);

        $result = $this->cacheManager->get($key);

        $this->assertNull($result);
    }

    public function test_get_returns_null_when_data_is_empty(): void {
        $key = 'empty_key';

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn('');

        $result = $this->cacheManager->get($key);

        $this->assertNull($result);
    }

    public function test_get_throws_exception_when_factory_produces_invalid_model(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('does not produce a CacheableModel');

        $key = 'test_invalid';
        $storedData = json_encode([
            CacheManager::DATA_KEY => ['test' => 'data'],
            CacheManager::FACTORY_KEY => InvalidFactoryForTest::class,
        ]);

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($storedData);

        App::shouldReceive('make')
            ->once()
            ->with(InvalidFactoryForTest::class)
            ->andReturn(new InvalidFactoryForTest());

        $this->cacheManager->get($key);
    }
}

class InvalidFactoryForTest {
    public function make() {
        return new \stdClass();
    }
}
