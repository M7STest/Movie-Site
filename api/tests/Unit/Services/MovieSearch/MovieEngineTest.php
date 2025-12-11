<?php

namespace Tests\Unit\Services\MovieSearch;

use App\Services\MovieSearch\Cache\CacheManager;
use App\Services\MovieSearch\Cache\EmptyCache;
use App\Services\MovieSearch\Factories\EmptyCacheFactory;
use App\Services\MovieSearch\Factories\SearchParamsFactory;
use App\Services\MovieSearch\Models\Movie;
use App\Services\MovieSearch\Models\SearchParams;
use App\Services\MovieSearch\Models\SearchResults;
use App\Services\MovieSearch\MovieEngine;
use App\Services\MovieSearch\OMDB\OMDBClient;
use App\Services\MovieSearch\Queue\LastViewedMovieQueue;
use Tests\TestCase;

class MovieEngineTest extends TestCase {

    private MovieEngine $engine;
    private OMDBClient $omdbClient;
    private CacheManager $cacheManager;
    private SearchParamsFactory $searchParamsFactory;
    private EmptyCacheFactory $emptyCacheFactory;
    private LastViewedMovieQueue $lastViewedMovieQueue;

    protected function setUp(): void {
        parent::setUp();
        
        $this->omdbClient = $this->createMock(OMDBClient::class);
        $this->cacheManager = $this->createMock(CacheManager::class);
        $this->searchParamsFactory = $this->createMock(SearchParamsFactory::class);
        $this->emptyCacheFactory = $this->createMock(EmptyCacheFactory::class);
        $this->lastViewedMovieQueue = $this->createMock(LastViewedMovieQueue::class);
        
        $this->engine = new MovieEngine(
            $this->omdbClient,
            $this->cacheManager,
            $this->searchParamsFactory,
            $this->emptyCacheFactory,
            $this->lastViewedMovieQueue
        );
    }

    public function test_search_movie_returns_cached_results_when_available(): void {
        $mockSearchParams = $this->createMock(SearchParams::class);
        $mockSearchParams->method('__toString')->willReturn('batman_movie_2008_1');
        
        $this->searchParamsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockSearchParams);
        
        $mockSearchParams->expects($this->once())
            ->method('setTitle')
            ->with('Batman')
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setType')
            ->with('movie')
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setYear')
            ->with(2008)
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setPage')
            ->with(1)
            ->willReturnSelf();

        $cachedResults = $this->createMock(SearchResults::class);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->with('movie_search_results_batman_movie_2008_1')
            ->willReturn($cachedResults);
        
        $this->omdbClient
            ->expects($this->never())
            ->method('searchMovies');
        
        $result = $this->engine->searchMovie('Batman', 'movie', 2008, 1);
        
        $this->assertSame($cachedResults, $result);
    }

    public function test_search_movie_fetches_from_api_when_not_cached(): void {
        $mockSearchParams = $this->createMock(SearchParams::class);
        $mockSearchParams->method('__toString')->willReturn('inception_null_null_1');
        
        $this->searchParamsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockSearchParams);
        
        $mockSearchParams->method('setTitle')->willReturnSelf();
        $mockSearchParams->method('setType')->willReturnSelf();
        $mockSearchParams->method('setYear')->willReturnSelf();
        $mockSearchParams->method('setPage')->willReturnSelf();

        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $apiResults = $this->createMock(SearchResults::class);
        
        $this->omdbClient
            ->expects($this->once())
            ->method('searchMovies')
            ->with($mockSearchParams)
            ->willReturn($apiResults);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('store')
            ->with($apiResults);
        
        $result = $this->engine->searchMovie('Inception', null, null, 1);
        
        $this->assertSame($apiResults, $result);
    }

    public function test_get_movie_returns_cached_movie(): void {
        $cachedMovie = $this->createMock(Movie::class);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->with('movie_tt0468569')
            ->willReturn($cachedMovie);
        
        $this->lastViewedMovieQueue
            ->expects($this->once())
            ->method('add')
            ->with($cachedMovie);
        
        $this->omdbClient
            ->expects($this->never())
            ->method('getMovieByID');
        
        $result = $this->engine->getMovie('tt0468569');
        
        $this->assertSame($cachedMovie, $result);
    }

    public function test_get_movie_returns_null_for_empty_cache(): void {
        $emptyCache = $this->createMock(EmptyCache::class);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->with('movie_tt9999999')
            ->willReturn($emptyCache);
        
        $this->omdbClient
            ->expects($this->never())
            ->method('getMovieByID');
        
        $this->lastViewedMovieQueue
            ->expects($this->never())
            ->method('add');
        
        $result = $this->engine->getMovie('tt9999999');
        
        $this->assertNull($result);
    }

    public function test_get_movie_fetches_from_api_when_not_cached(): void {
        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->with('movie_tt1375666')
            ->willReturn(null);

        $apiMovie = $this->createMock(Movie::class);
        
        $this->omdbClient
            ->expects($this->once())
            ->method('getMovieByID')
            ->with('tt1375666')
            ->willReturn($apiMovie);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('store')
            ->with($apiMovie);
        
        $this->lastViewedMovieQueue
            ->expects($this->once())
            ->method('add')
            ->with($apiMovie);
        
        $result = $this->engine->getMovie('tt1375666');
        
        $this->assertSame($apiMovie, $result);
    }

    public function test_get_movie_stores_empty_cache_when_api_returns_null(): void {
        $this->cacheManager
            ->expects($this->once())
            ->method('get')
            ->with('movie_tt0000000')
            ->willReturn(null);

        $this->omdbClient
            ->expects($this->once())
            ->method('getMovieByID')
            ->with('tt0000000')
            ->willReturn(null);

        $mockEmptyCache = $this->createMock(EmptyCache::class);
        $mockEmptyCache->expects($this->once())
            ->method('setKey')
            ->with('movie_tt0000000')
            ->willReturnSelf();
        
        $this->emptyCacheFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockEmptyCache);
        
        $this->cacheManager
            ->expects($this->once())
            ->method('store')
            ->with($mockEmptyCache);
        
        $this->lastViewedMovieQueue
            ->expects($this->never())
            ->method('add');
        
        $result = $this->engine->getMovie('tt0000000');
        
        $this->assertNull($result);
    }

    public function test_get_last_viewed_movies_returns_array_of_movies(): void {
        $movieIds = ['tt0468569', 'tt1375666', 'tt0816692'];
        
        $this->lastViewedMovieQueue
            ->expects($this->once())
            ->method('getRecentMovies')
            ->willReturn($movieIds);

        $movie1 = $this->createMock(Movie::class);
        $movie2 = $this->createMock(Movie::class);
        $movie3 = $this->createMock(Movie::class);

        $this->cacheManager
            ->expects($this->exactly(3))
            ->method('get')
            ->willReturnOnConsecutiveCalls($movie1, $movie2, $movie3);
        
        $this->lastViewedMovieQueue
            ->expects($this->exactly(3))
            ->method('add');
        
        $result = $this->engine->getLastViewedMovies();
        
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame($movie1, $result[0]);
        $this->assertSame($movie2, $result[1]);
        $this->assertSame($movie3, $result[2]);
    }

    public function test_get_last_viewed_movies_returns_empty_array_when_no_movies(): void {
        $this->lastViewedMovieQueue
            ->expects($this->once())
            ->method('getRecentMovies')
            ->willReturn([]);
        
        $this->cacheManager
            ->expects($this->never())
            ->method('get');
        
        $result = $this->engine->getLastViewedMovies();
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_last_viewed_movies_skips_null_movies(): void {
        $movieIds = ['tt0001', 'tt0002', 'tt0003'];
        
        $this->lastViewedMovieQueue
            ->expects($this->once())
            ->method('getRecentMovies')
            ->willReturn($movieIds);

        $movie1 = $this->createMock(Movie::class);
        $emptyCache = $this->createMock(EmptyCache::class);
        $movie3 = $this->createMock(Movie::class);

        $this->cacheManager
            ->expects($this->exactly(3))
            ->method('get')
            ->willReturnOnConsecutiveCalls($movie1, $emptyCache, $movie3);
        
        $result = $this->engine->getLastViewedMovies();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame($movie1, $result[0]);
        $this->assertSame($movie3, $result[1]);
    }

    public function test_search_movie_with_all_parameters(): void {
        $mockSearchParams = $this->createMock(SearchParams::class);
        $mockSearchParams->method('__toString')->willReturn('star_wars_movie_1977_2');
        
        $this->searchParamsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockSearchParams);
        
        $mockSearchParams->expects($this->once())
            ->method('setTitle')
            ->with('Star Wars')
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setType')
            ->with('movie')
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setYear')
            ->with(1977)
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setPage')
            ->with(2)
            ->willReturnSelf();

        $this->cacheManager
            ->method('get')
            ->willReturn(null);

        $apiResults = $this->createMock(SearchResults::class);
        
        $this->omdbClient
            ->expects($this->once())
            ->method('searchMovies')
            ->with($mockSearchParams)
            ->willReturn($apiResults);
        
        $result = $this->engine->searchMovie('Star Wars', 'movie', 1977, 2);
        
        $this->assertSame($apiResults, $result);
    }

    public function test_search_movie_with_minimal_parameters(): void {
        $mockSearchParams = $this->createMock(SearchParams::class);
        $mockSearchParams->method('__toString')->willReturn('avatar_null_null_null');
        
        $this->searchParamsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockSearchParams);
        
        $mockSearchParams->expects($this->once())
            ->method('setTitle')
            ->with('Avatar')
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setType')
            ->with(null)
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setYear')
            ->with(null)
            ->willReturnSelf();
        
        $mockSearchParams->expects($this->once())
            ->method('setPage')
            ->with(null)
            ->willReturnSelf();

        $this->cacheManager
            ->method('get')
            ->willReturn(null);

        $apiResults = $this->createMock(SearchResults::class);
        
        $this->omdbClient
            ->method('searchMovies')
            ->willReturn($apiResults);
        
        $result = $this->engine->searchMovie('Avatar', null, null, null);
        
        $this->assertInstanceOf(SearchResults::class, $result);
    }
}
