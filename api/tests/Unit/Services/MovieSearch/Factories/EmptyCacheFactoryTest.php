<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Cache\EmptyCache;
use App\Services\MovieSearch\Factories\EmptyCacheFactory;
use Tests\TestCase;

class EmptyCacheFactoryTest extends TestCase {

    public function test_make_returns_empty_cache_instance(): void {
        $factory = new EmptyCacheFactory();
        
        $result = $factory->make();
        
        $this->assertInstanceOf(EmptyCache::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $factory = new EmptyCacheFactory();
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }
}
