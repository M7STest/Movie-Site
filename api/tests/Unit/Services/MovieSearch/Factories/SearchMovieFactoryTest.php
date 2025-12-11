<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Models\SearchMovie;
use Tests\TestCase;

class SearchMovieFactoryTest extends TestCase {

    public function test_make_returns_search_movie_instance(): void {
        $factory = new SearchMovieFactory();
        
        $result = $factory->make();
        
        $this->assertInstanceOf(SearchMovie::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $factory = new SearchMovieFactory();
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }
}
