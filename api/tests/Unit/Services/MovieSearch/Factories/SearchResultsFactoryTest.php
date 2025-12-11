<?php

namespace Tests\Unit\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Factories\SearchMetaFactory;
use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Factories\SearchParamsFactory;
use App\Services\MovieSearch\Factories\SearchResultsFactory;
use App\Services\MovieSearch\Models\SearchResults;
use Tests\TestCase;

class SearchResultsFactoryTest extends TestCase {

    public function test_make_returns_search_results_instance(): void {
        $searchParamsFactory = new SearchParamsFactory();
        $searchMetaFactory = new SearchMetaFactory();
        $searchMovieFactory = new SearchMovieFactory();
        
        $factory = new SearchResultsFactory(
            $searchParamsFactory,
            $searchMetaFactory,
            $searchMovieFactory
        );
        
        $result = $factory->make();
        
        $this->assertInstanceOf(SearchResults::class, $result);
    }

    public function test_make_returns_new_instance_each_time(): void {
        $searchParamsFactory = new SearchParamsFactory();
        $searchMetaFactory = new SearchMetaFactory();
        $searchMovieFactory = new SearchMovieFactory();
        
        $factory = new SearchResultsFactory(
            $searchParamsFactory,
            $searchMetaFactory,
            $searchMovieFactory
        );
        
        $instance1 = $factory->make();
        $instance2 = $factory->make();
        
        $this->assertNotSame($instance1, $instance2);
    }

    public function test_make_injects_dependencies_into_search_results(): void {
        $searchParamsFactory = new SearchParamsFactory();
        $searchMetaFactory = new SearchMetaFactory();
        $searchMovieFactory = new SearchMovieFactory();
        
        $factory = new SearchResultsFactory(
            $searchParamsFactory,
            $searchMetaFactory,
            $searchMovieFactory
        );
        
        $result = $factory->make();
        
        $meta = $result->getMeta();
        $this->assertNotNull($meta);
    }
}
