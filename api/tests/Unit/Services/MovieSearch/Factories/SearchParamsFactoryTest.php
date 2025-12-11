<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Factories\SearchParamsFactory;
use App\Services\MovieSearch\Models\SearchParams;
use Tests\TestCase;

class SearchParamsFactoryTest extends TestCase {

    public function test_make_returns_search_params_instance(): void {
        $factory = new SearchParamsFactory();
        
        $result = $factory->make();
        
        $this->assertInstanceOf(SearchParams::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $factory = new SearchParamsFactory();
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }
}
