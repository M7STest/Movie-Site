<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Factories\SearchMetaFactory;
use App\Services\MovieSearch\Models\SearchMeta;
use Tests\TestCase;

class SearchMetaFactoryTest extends TestCase {

    public function test_make_returns_search_meta_instance(): void {
        $factory = new SearchMetaFactory();
        
        $result = $factory->make();
        
        $this->assertInstanceOf(SearchMeta::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $factory = new SearchMetaFactory();
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }
}
