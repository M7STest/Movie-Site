<?php

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\MovieResponse;
use App\Services\MovieSearch\Models\Movie;
use Tests\TestCase;

class MovieResponseTest extends TestCase {

    private MovieResponse $response;

    protected function setUp(): void {
        parent::setUp();
        
        $this->response = new MovieResponse();
    }

    public function test_create_response_returns_self(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Test Movie');

        $result = $this->response->createResponse($movie);

        $this->assertSame($this->response, $result);
    }

    public function test_create_response_includes_all_movie_data(): void {
        $movie = $this->createMock(Movie::class);
        
        $movie->method('getTitle')->willReturn('The Dark Knight');
        $movie->method('getYear')->willReturn('2008');
        $movie->method('getRated')->willReturn('PG-13');
        $movie->method('getReleased')->willReturn('18 Jul 2008');
        $movie->method('getRuntime')->willReturn('152 min');
        $movie->method('getGenre')->willReturn('Action, Crime, Drama');
        $movie->method('getDirector')->willReturn('Christopher Nolan');
        $movie->method('getWriter')->willReturn('Jonathan Nolan');
        $movie->method('getActors')->willReturn('Christian Bale, Heath Ledger');
        $movie->method('getPlot')->willReturn('When the menace known as the Joker...');
        $movie->method('getLanguage')->willReturn('English');
        $movie->method('getCountry')->willReturn('USA, UK');
        $movie->method('getAwards')->willReturn('Won 2 Oscars');
        $movie->method('getPoster')->willReturn('https://example.com/poster.jpg');
        $movie->method('getRatingImdb')->willReturn('9.0/10');
        $movie->method('getRatingRottenTomatoes')->willReturn('94%');
        $movie->method('getRatingMetacritic')->willReturn('84/100');
        $movie->method('getMetascore')->willReturn('84');
        $movie->method('getImdbRating')->willReturn('9.0');
        $movie->method('getImdbVotes')->willReturn('2,800,000');
        $movie->method('getImdbID')->willReturn('tt0468569');
        $movie->method('getType')->willReturn('movie');
        $movie->method('getDvd')->willReturn('09 Dec 2008');
        $movie->method('getBoxOffice')->willReturn('$534,987,076');
        $movie->method('getProduction')->willReturn('Warner Bros.');
        $movie->method('getWebsite')->willReturn('N/A');

        $this->response->createResponse($movie);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('The Dark Knight', $data['data']['title']);
        $this->assertEquals('2008', $data['data']['year']);
        $this->assertEquals('PG-13', $data['data']['rated']);
        $this->assertEquals('Christopher Nolan', $data['data']['director']);
        $this->assertEquals('tt0468569', $data['data']['imdbID']);
    }

    public function test_create_response_includes_ratings_object(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Movie');
        $movie->method('getRatingImdb')->willReturn('8.5/10');
        $movie->method('getRatingRottenTomatoes')->willReturn('90%');
        $movie->method('getRatingMetacritic')->willReturn('85/100');

        $this->response->createResponse($movie);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('ratings', $data['data']);
        $this->assertEquals('8.5/10', $data['data']['ratings']['imdb']);
        $this->assertEquals('90%', $data['data']['ratings']['rottenTomatoes']);
        $this->assertEquals('85/100', $data['data']['ratings']['metacritic']);
    }

    public function test_create_response_with_null_values(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Movie Title');
        $movie->method('getYear')->willReturn(null);
        $movie->method('getRated')->willReturn(null);
        $movie->method('getRatingImdb')->willReturn(null);

        $this->response->createResponse($movie);
        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertNull($data['data']['year']);
        $this->assertNull($data['data']['rated']);
        $this->assertNull($data['data']['ratings']['imdb']);
    }

    public function test_response_status_code_is_200(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Test');

        $this->response->createResponse($movie);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function test_response_is_json(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getTitle')->willReturn('Test');

        $this->response->createResponse($movie);

        $this->assertStringContainsString('application/json', $this->response->headers->get('Content-Type'));
    }
}
