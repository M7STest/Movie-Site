<?php

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\LastMoviesResponse;
use App\Services\MovieSearch\Models\Movie;
use Tests\TestCase;

class LastMoviesResponseTest extends TestCase {

    private LastMoviesResponse $response;

    protected function setUp(): void {
        parent::setUp();
        
        $this->response = new LastMoviesResponse();
    }

    public function test_create_response_returns_self(): void {
        $result = $this->response->createResponse([]);

        $this->assertSame($this->response, $result);
    }

    public function test_create_response_with_multiple_movies(): void {
        $movie1 = $this->createMock(Movie::class);
        $movie1->method('getTitle')->willReturn('The Dark Knight');
        $movie1->method('getYear')->willReturn('2008');
        $movie1->method('getImdbID')->willReturn('tt0468569');
        $movie1->method('getType')->willReturn('movie');
        $movie1->method('getPoster')->willReturn('poster1.jpg');

        $movie2 = $this->createMock(Movie::class);
        $movie2->method('getTitle')->willReturn('Inception');
        $movie2->method('getYear')->willReturn('2010');
        $movie2->method('getImdbID')->willReturn('tt1375666');
        $movie2->method('getType')->willReturn('movie');
        $movie2->method('getPoster')->willReturn('poster2.jpg');

        $movie3 = $this->createMock(Movie::class);
        $movie3->method('getTitle')->willReturn('Interstellar');
        $movie3->method('getYear')->willReturn('2014');
        $movie3->method('getImdbID')->willReturn('tt0816692');
        $movie3->method('getType')->willReturn('movie');
        $movie3->method('getPoster')->willReturn('poster3.jpg');

        $this->response->createResponse([$movie1, $movie2, $movie3]);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertCount(3, $data['data']);
        
        $this->assertEquals('The Dark Knight', $data['data'][0]['title']);
        $this->assertEquals('tt0468569', $data['data'][0]['id']);
        
        $this->assertEquals('Inception', $data['data'][1]['title']);
        $this->assertEquals('tt1375666', $data['data'][1]['id']);
        
        $this->assertEquals('Interstellar', $data['data'][2]['title']);
        $this->assertEquals('tt0816692', $data['data'][2]['id']);
    }

    public function test_create_response_with_empty_array(): void {
        $this->response->createResponse([]);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
        $this->assertEquals(0, $data['meta']['count']);
    }

    public function test_create_response_includes_meta_count(): void {
        $movie1 = $this->createMock(Movie::class);
        $movie1->method('getTitle')->willReturn('Movie 1');
        $movie1->method('getYear')->willReturn('2020');
        $movie1->method('getImdbID')->willReturn('tt0001');
        $movie1->method('getType')->willReturn('movie');
        $movie1->method('getPoster')->willReturn('poster.jpg');

        $movie2 = $this->createMock(Movie::class);
        $movie2->method('getTitle')->willReturn('Movie 2');
        $movie2->method('getYear')->willReturn('2021');
        $movie2->method('getImdbID')->willReturn('tt0002');
        $movie2->method('getType')->willReturn('movie');
        $movie2->method('getPoster')->willReturn('poster.jpg');

        $this->response->createResponse([$movie1, $movie2]);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('count', $data['meta']);
        $this->assertEquals(2, $data['meta']['count']);
    }

    public function test_movie_data_structure(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Test Movie');
        $movie->method('getYear')->willReturn('2025');
        $movie->method('getImdbID')->willReturn('tt1234567');
        $movie->method('getType')->willReturn('movie');
        $movie->method('getPoster')->willReturn('https://example.com/poster.jpg');

        $this->response->createResponse([$movie]);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('title', $data['data'][0]);
        $this->assertArrayHasKey('year', $data['data'][0]);
        $this->assertArrayHasKey('id', $data['data'][0]);
        $this->assertArrayHasKey('type', $data['data'][0]);
        $this->assertArrayHasKey('poster', $data['data'][0]);
    }

    public function test_response_status_code_is_200(): void {
        $this->response->createResponse([]);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function test_response_is_json(): void {
        $this->response->createResponse([]);

        $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
    }

    public function test_create_response_with_five_movies(): void {
        $movies = [];
        for ($i = 1; $i <= 5; $i++) {
            $movie = $this->createMock(Movie::class);
            $movie->method('getTitle')->willReturn("Movie $i");
            $movie->method('getYear')->willReturn((string)(2020 + $i));
            $movie->method('getImdbID')->willReturn("tt000000$i");
            $movie->method('getType')->willReturn('movie');
            $movie->method('getPoster')->willReturn("poster$i.jpg");
            $movies[] = $movie;
        }

        $this->response->createResponse($movies);
        $data = json_decode($this->response->getContent(), true);

        $this->assertCount(5, $data['data']);
        $this->assertEquals(5, $data['meta']['count']);
    }

    public function test_create_response_with_null_values(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Test Movie');
        $movie->method('getYear')->willReturn(null);
        $movie->method('getImdbID')->willReturn('tt1234567');
        $movie->method('getType')->willReturn(null);
        $movie->method('getPoster')->willReturn(null);

        $this->response->createResponse([$movie]);
        $data = json_decode($this->response->getContent(), true);

        $this->assertNull($data['data'][0]['year']);
        $this->assertNull($data['data'][0]['type']);
        $this->assertNull($data['data'][0]['poster']);
    }
}
