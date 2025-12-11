<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Factories\MovieFactory;
use App\Services\MovieSearch\Models\Movie;
use Tests\TestCase;

class MovieFactoryTest extends TestCase {

    public function test_make_returns_movie_instance(): void {
        $factory = new MovieFactory();
        
        $result = $factory->make();
        
        $this->assertInstanceOf(Movie::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $factory = new MovieFactory();
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }
}
