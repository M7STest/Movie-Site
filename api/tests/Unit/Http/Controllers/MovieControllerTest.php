<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\MovieController;
use App\Http\Requests\SearchMovieRequest;
use App\Http\Responses\LastMoviesResponse;
use App\Http\Responses\MovieResponse;
use App\Http\Responses\SearchMovieResponse;
use App\Services\MovieSearch\Models\Movie;
use App\Services\MovieSearch\Models\SearchResults;
use App\Services\MovieSearch\MovieEngine;
use Tests\TestCase;

class MovieControllerTest extends TestCase {

    private MovieController $controller;
    private MovieEngine $movieEngine;

    protected function setUp(): void {
        parent::setUp();
        
        $this->movieEngine = $this->createMock(MovieEngine::class);
        $this->controller = new MovieController($this->movieEngine);
    }

    public function test_search_returns_search_results(): void {
        $request = SearchMovieRequest::create('/search', 'GET', [
            'title' => 'Batman',
            'type' => 'movie',
            'year' => 2008,
            'page' => 1,
        ]);

        $searchResults = $this->createMock(SearchResults::class);
        
        $this->movieEngine
            ->expects($this->once())
            ->method('searchMovie')
            ->with('Batman', 'movie', 2008, 1)
            ->willReturn($searchResults);

        $response = new SearchMovieResponse();
        
        $result = $this->controller->search($request, $response);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
    }

    public function test_get_returns_movie_when_found(): void {
        $movie = $this->createMock(Movie::class);
        
        $this->movieEngine
            ->expects($this->once())
            ->method('getMovie')
            ->with('tt0468569')
            ->willReturn($movie);

        $response = new MovieResponse();
        
        $result = $this->controller->get('tt0468569', $response);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_get_returns_404_when_movie_not_found(): void {
        $this->movieEngine
            ->expects($this->once())
            ->method('getMovie')
            ->with('tt9999999')
            ->willReturn(null);

        $response = new MovieResponse();
        
        $result = $this->controller->get('tt9999999', $response);

        $this->assertEquals(404, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Movie not found', $data['error']);
    }

    public function test_get_returns_400_with_empty_imdb_id(): void {
        $this->movieEngine
            ->expects($this->never())
            ->method('getMovie');

        $response = new MovieResponse();
        
        $result = $this->controller->get('', $response);

        $this->assertEquals(400, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('IMDb ID is required', $data['error']);
    }

    public function test_recent_returns_last_viewed_movies(): void {
        $movies = [
            $this->createMock(Movie::class),
            $this->createMock(Movie::class),
        ];
        
        $this->movieEngine
            ->expects($this->once())
            ->method('getLastViewedMovies')
            ->willReturn($movies);

        $response = new LastMoviesResponse();
        
        $result = $this->controller->recent($response);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_recent_returns_empty_array_when_no_movies(): void {
        $this->movieEngine
            ->expects($this->once())
            ->method('getLastViewedMovies')
            ->willReturn([]);

        $response = new LastMoviesResponse();
        
        $result = $this->controller->recent($response);

        $this->assertEquals(200, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
    }

    public function test_search_with_minimal_parameters(): void {
        $request = SearchMovieRequest::create('/search', 'GET', [
            'title' => 'Inception',
        ]);

        $searchResults = $this->createMock(SearchResults::class);
        
        $this->movieEngine
            ->expects($this->once())
            ->method('searchMovie')
            ->with('Inception', null, null, null)
            ->willReturn($searchResults);

        $response = new SearchMovieResponse();
        
        $result = $this->controller->search($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }
}
