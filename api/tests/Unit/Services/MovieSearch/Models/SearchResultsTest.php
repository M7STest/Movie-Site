<?php

namespace Tests\Unit\Services\MovieSearch\Models;

use App\Services\MovieSearch\Factories\SearchMetaFactory;
use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Factories\SearchParamsFactory;
use App\Services\MovieSearch\Models\SearchMovie;
use App\Services\MovieSearch\Models\SearchParams;
use App\Services\MovieSearch\Models\SearchResults;
use Tests\TestCase;

class SearchResultsTest extends TestCase {

    private SearchResults $searchResults;

    protected function setUp(): void {
        parent::setUp();
        
        $searchParamsFactory = new SearchParamsFactory();
        $searchMetaFactory = new SearchMetaFactory();
        $searchMovieFactory = new SearchMovieFactory();
        
        $this->searchResults = new SearchResults(
            $searchParamsFactory,
            $searchMetaFactory,
            $searchMovieFactory
        );
    }

    public function test_get_movies_returns_empty_array_by_default(): void {
        $this->assertEquals([], $this->searchResults->getMovies());
    }

    public function test_add_movie(): void {
        $movie = new SearchMovie();
        $movie->setTitle('Test Movie');
        
        $result = $this->searchResults->addMovie($movie);
        
        $this->assertSame($this->searchResults, $result); // Fluent interface
        $this->assertCount(1, $this->searchResults->getMovies());
        $this->assertSame($movie, $this->searchResults->getMovies()[0]);
    }

    public function test_add_multiple_movies(): void {
        $movie1 = new SearchMovie();
        $movie1->setTitle('Movie 1');
        
        $movie2 = new SearchMovie();
        $movie2->setTitle('Movie 2');
        
        $this->searchResults->addMovie($movie1)->addMovie($movie2);
        
        $movies = $this->searchResults->getMovies();
        $this->assertCount(2, $movies);
        $this->assertSame($movie1, $movies[0]);
        $this->assertSame($movie2, $movies[1]);
    }

    public function test_get_meta_returns_instance(): void {
        $meta = $this->searchResults->getMeta();
        
        $this->assertNotNull($meta);
        $this->assertInstanceOf(\App\Services\MovieSearch\Models\SearchMeta::class, $meta);
    }

    public function test_set_search_params(): void {
        $params = new SearchParams();
        $params->setTitle('Batman');
        
        $result = $this->searchResults->setSearchParams($params);
        
        $this->assertSame($this->searchResults, $result);
        $this->assertSame($params, $this->searchResults->getSearchParams());
    }

    public function test_get_cache_key(): void {
        $params = new SearchParams();
        $params->setTitle('test');
        
        $this->searchResults->setSearchParams($params);
        
        $cacheKey = $this->searchResults->getCacheKey();
        
        $this->assertIsString($cacheKey);
        $this->assertNotEmpty($cacheKey);
    }

    public function test_get_full_cache_key_includes_prefix(): void {
        $params = new SearchParams();
        $params->setTitle('batman');
        
        $this->searchResults->setSearchParams($params);
        
        $fullKey = $this->searchResults->getFullCacheKey();
        
        $this->assertStringStartsWith('movie_search_results_', $fullKey);
    }

    public function test_get_and_set_data(): void {
        $movie = new SearchMovie();
        $movie->setTitle('Test Movie')
            ->setYear('2024')
            ->setImdbID('tt0000001')
            ->setType('movie')
            ->setPoster('https://example.com/poster.jpg');
        
        $this->searchResults->addMovie($movie);
        $this->searchResults->getMeta()->setTotal(1);
        
        $data = $this->searchResults->getData();
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('movies', $data);
        $this->assertArrayHasKey('meta', $data);
        
        // Create new instance and restore data
        $newResults = new SearchResults(
            new SearchParamsFactory(),
            new SearchMetaFactory(),
            new SearchMovieFactory()
        );
        
        $newResults->setData($data);
        
        $this->assertCount(1, $newResults->getMovies());
        $this->assertEquals(1, $newResults->getMeta()->getTotal());
    }

    public function test_get_factory_class(): void {
        $factoryClass = SearchResults::getFactoryClass();
        
        $this->assertEquals(\App\Services\MovieSearch\Factories\SearchResultsFactory::class, $factoryClass);
    }
}
