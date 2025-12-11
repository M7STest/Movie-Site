<?php

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\SearchMovieResponse;
use App\Services\MovieSearch\Models\SearchMovie;
use App\Services\MovieSearch\Models\SearchMeta;
use App\Services\MovieSearch\Models\SearchResults;
use Tests\TestCase;

class SearchMovieResponseTest extends TestCase {

    private SearchMovieResponse $response;

    protected function setUp(): void {
        parent::setUp();
        
        $this->response = new SearchMovieResponse();
    }

    public function test_create_response_returns_self(): void {
        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([]);
        
        $meta = $this->createMock(SearchMeta::class);
        $searchResults->method('getMeta')->willReturn($meta);

        $result = $this->response->createResponse($searchResults);

        $this->assertSame($this->response, $result);
    }

    public function test_create_response_with_multiple_movies(): void {
        $movie1 = $this->createMock(SearchMovie::class);
        $movie1->method('getImdbID')->willReturn('tt0001');
        $movie1->method('getTitle')->willReturn('Movie 1');
        $movie1->method('getYear')->willReturn('2020');
        $movie1->method('getType')->willReturn('movie');
        $movie1->method('getPoster')->willReturn('poster1.jpg');

        $movie2 = $this->createMock(SearchMovie::class);
        $movie2->method('getImdbID')->willReturn('tt0002');
        $movie2->method('getTitle')->willReturn('Movie 2');
        $movie2->method('getYear')->willReturn('2021');
        $movie2->method('getType')->willReturn('series');
        $movie2->method('getPoster')->willReturn('poster2.jpg');

        $meta = $this->createMock(SearchMeta::class);
        $meta->method('getTotal')->willReturn(100);
        $meta->method('getPerPage')->willReturn(10);
        $meta->method('getPages')->willReturn(10);
        $meta->method('getCurrentPage')->willReturn(1);

        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([$movie1, $movie2]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertCount(2, $data['data']);
        
        $this->assertEquals('tt0001', $data['data'][0]['id']);
        $this->assertEquals('Movie 1', $data['data'][0]['title']);
        $this->assertEquals('2020', $data['data'][0]['year']);
        $this->assertEquals('movie', $data['data'][0]['type']);
        
        $this->assertEquals('tt0002', $data['data'][1]['id']);
        $this->assertEquals('Movie 2', $data['data'][1]['title']);
    }

    public function test_create_response_includes_meta_data(): void {
        $meta = $this->createMock(SearchMeta::class);
        $meta->method('getTotal')->willReturn(468);
        $meta->method('getPerPage')->willReturn(10);
        $meta->method('getPages')->willReturn(47);
        $meta->method('getCurrentPage')->willReturn(5);

        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('meta', $data);
        $this->assertEquals(468, $data['meta']['total']);
        $this->assertEquals(10, $data['meta']['perPage']);
        $this->assertEquals(47, $data['meta']['pages']);
        $this->assertEquals(5, $data['meta']['currentPage']);
    }

    public function test_create_response_with_empty_results(): void {
        $meta = $this->createMock(SearchMeta::class);
        $meta->method('getTotal')->willReturn(0);
        $meta->method('getPerPage')->willReturn(10);
        $meta->method('getPages')->willReturn(0);
        $meta->method('getCurrentPage')->willReturn(1);

        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
        $this->assertEquals(0, $data['meta']['total']);
    }

    public function test_response_status_code_is_200(): void {
        $meta = $this->createMock(SearchMeta::class);
        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function test_response_is_json(): void {
        $meta = $this->createMock(SearchMeta::class);
        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);

        $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
    }

    public function test_movie_data_structure(): void {
        $movie = $this->createMock(SearchMovie::class);
        $movie->method('getImdbID')->willReturn('tt1234567');
        $movie->method('getTitle')->willReturn('Test Movie');
        $movie->method('getYear')->willReturn('2025');
        $movie->method('getType')->willReturn('movie');
        $movie->method('getPoster')->willReturn('https://example.com/poster.jpg');

        $meta = $this->createMock(SearchMeta::class);

        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getMovies')->willReturn([$movie]);
        $searchResults->method('getMeta')->willReturn($meta);

        $this->response->createResponse($searchResults);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('id', $data['data'][0]);
        $this->assertArrayHasKey('title', $data['data'][0]);
        $this->assertArrayHasKey('year', $data['data'][0]);
        $this->assertArrayHasKey('type', $data['data'][0]);
        $this->assertArrayHasKey('poster', $data['data'][0]);
    }
}
