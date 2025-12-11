<?php

namespace Tests\Unit\Services\MovieSearch\OMDB;

use App\Services\MovieSearch\Factories\MovieFactory;
use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Factories\SearchResultsFactory;
use App\Services\MovieSearch\Models\SearchParams;
use App\Services\MovieSearch\Models\SearchResults;
use App\Services\MovieSearch\OMDB\OMDBClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OMDBClientTest extends TestCase {

    private OMDBClient $client;
    private SearchResultsFactory $searchResultsFactory;
    private SearchMovieFactory $searchMovieFactory;
    private MovieFactory $movieFactory;

    protected function setUp(): void {
        parent::setUp();
        
        $this->searchResultsFactory = $this->createMock(SearchResultsFactory::class);
        $this->searchMovieFactory = $this->createMock(SearchMovieFactory::class);
        $this->movieFactory = $this->createMock(MovieFactory::class);
        
        $this->client = new OMDBClient(
            $this->searchResultsFactory, 
            $this->searchMovieFactory, 
            $this->movieFactory
        );
    }

    public function test_search_movies_with_successful_response(): void {
        $searchParams = (new SearchParams())->setTitle('Batman');
        
        $apiResponse = [
            'Search' => [
                ['Title' => 'Batman', 'Year' => '1989', 'imdbID' => 'tt0096895'],
                ['Title' => 'Batman Begins', 'Year' => '2005', 'imdbID' => 'tt0372784'],
            ],
            'totalResults' => '468',
            'Response' => 'True',
        ];

        Http::fake([
            'www.omdbapi.com*' => Http::response($apiResponse, 200),
        ]);

        $expectedResult = $this->createMock(SearchResults::class);
        
        $this->searchResultsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($expectedResult);

        $mockSearchMovie1 = $this->createMock(\App\Services\MovieSearch\Models\SearchMovie::class);
        $mockSearchMovie1->expects($this->once())->method('setTitle')->with('Batman')->willReturnSelf();
        $mockSearchMovie1->expects($this->once())->method('setYear')->with('1989')->willReturnSelf();
        $mockSearchMovie1->expects($this->once())->method('setImdbID')->with('tt0096895')->willReturnSelf();
        $mockSearchMovie1->expects($this->once())->method('setType')->willReturnSelf();
        $mockSearchMovie1->expects($this->once())->method('setPoster')->willReturnSelf();

        $mockSearchMovie2 = $this->createMock(\App\Services\MovieSearch\Models\SearchMovie::class);
        $mockSearchMovie2->expects($this->once())->method('setTitle')->with('Batman Begins')->willReturnSelf();
        $mockSearchMovie2->expects($this->once())->method('setYear')->with('2005')->willReturnSelf();
        $mockSearchMovie2->expects($this->once())->method('setImdbID')->with('tt0372784')->willReturnSelf();
        $mockSearchMovie2->expects($this->once())->method('setType')->willReturnSelf();
        $mockSearchMovie2->expects($this->once())->method('setPoster')->willReturnSelf();

        $this->searchMovieFactory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturnOnConsecutiveCalls($mockSearchMovie1, $mockSearchMovie2);

        $expectedResult
            ->expects($this->once())
            ->method('setSearchParams')
            ->with($searchParams)
            ->willReturnSelf();

        $addedMovies = [];
        $expectedResult
            ->expects($this->exactly(2))
            ->method('addMovie')
            ->willReturnCallback(function($movie) use (&$addedMovies, $expectedResult) {
                $addedMovies[] = $movie;
                return $expectedResult;
            });

        $mockMeta = $this->createMock(\App\Services\MovieSearch\Models\SearchMeta::class);
        $mockMeta->method('setCurrentPage')->willReturnSelf();
        $mockMeta->method('setPerPage')->willReturnSelf();
        $mockMeta->method('setTotal')->willReturnSelf();
        $mockMeta->method('setPages')->willReturnSelf();
        $mockMeta->method('getPerPage')->willReturn(10);
        $mockMeta->method('getTotal')->willReturn(468);

        $expectedResult
            ->expects($this->once())
            ->method('getMeta')
            ->willReturn($mockMeta);
        
        
        $result = $this->client->searchMovies($searchParams);

        $this->assertSame($expectedResult, $result);
        $this->assertCount(2, $addedMovies);
        $this->assertSame($mockSearchMovie1, $addedMovies[0]);
        $this->assertSame($mockSearchMovie2, $addedMovies[1]);
    }

    public function test_get_movie_by_id_with_failed_response(): void {
        $imdbId = 'invalid_id';

        Http::fake([
            'www.omdbapi.com*' => Http::response(['Response' => 'False', 'Error' => 'Invalid ID'], 404),
        ]);

        $result = $this->client->getMovieByID($imdbId);

        $this->assertNull($result);
    }

    public function test_get_movie_by_id_with_successful_response(): void {
        $imdbId = 'tt0468569';

        $apiResponse = [
            'Title' => 'The Dark Knight',
            'Year' => '2008',
            'Rated' => 'PG-13',
            'Released' => '18 Jul 2008',
            'Runtime' => '152 min',
            'Genre' => 'Action, Crime, Drama',
            'Director' => 'Christopher Nolan',
            'Writer' => 'Jonathan Nolan, Christopher Nolan',
            'Actors' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
            'Plot' => 'When the menace known as the Joker wreaks havoc...',
            'Language' => 'English, Mandarin',
            'Country' => 'United States, United Kingdom',
            'Awards' => 'Won 2 Oscars',
            'Poster' => 'https://example.com/poster.jpg',
            'Ratings' => [
                ['Source' => 'Internet Movie Database', 'Value' => '9.0/10'],
                ['Source' => 'Rotten Tomatoes', 'Value' => '94%'],
                ['Source' => 'Metacritic', 'Value' => '84/100'],
            ],
            'Metascore' => '84',
            'imdbRating' => '9.0',
            'imdbVotes' => '2,800,000',
            'imdbID' => 'tt0468569',
            'Type' => 'movie',
            'DVD' => '09 Dec 2008',
            'BoxOffice' => '$534,987,076',
            'Production' => 'Warner Bros.',
            'Website' => 'N/A',
            'Response' => 'True',
        ];

        Http::fake([
            'www.omdbapi.com*' => Http::response($apiResponse, 200),
        ]);

        $mockMovie = $this->createMock(\App\Services\MovieSearch\Models\Movie::class);
        
        $mockMovie->expects($this->once())->method('setTitle')->with('The Dark Knight')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setYear')->with('2008')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setRated')->with('PG-13')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setReleased')->with('18 Jul 2008')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setRuntime')->with('152 min')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setGenre')->with('Action, Crime, Drama')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setDirector')->with('Christopher Nolan')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setWriter')->with('Jonathan Nolan, Christopher Nolan')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setActors')->with('Christian Bale, Heath Ledger, Aaron Eckhart')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setPlot')->with('When the menace known as the Joker wreaks havoc...')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setLanguage')->with('English, Mandarin')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setCountry')->with('United States, United Kingdom')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setAwards')->with('Won 2 Oscars')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setPoster')->with('https://example.com/poster.jpg')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setMetascore')->with('84')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setImdbRating')->with('9.0')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setImdbVotes')->with('2,800,000')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setImdbID')->with('tt0468569')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setType')->with('movie')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setDvd')->with('09 Dec 2008')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setBoxOffice')->with('$534,987,076')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setProduction')->with('Warner Bros.')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setWebsite')->with('N/A')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setResponse')->with('True')->willReturnSelf();
        
        // Flattened ratings
        $mockMovie->expects($this->once())->method('setRatingImdb')->with('9.0/10')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setRatingRottenTomatoes')->with('94%')->willReturnSelf();
        $mockMovie->expects($this->once())->method('setRatingMetacritic')->with('84/100')->willReturnSelf();

        $this->movieFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockMovie);

        $result = $this->client->getMovieByID($imdbId);

        $this->assertSame($mockMovie, $result);
    }

    public function test_search_movies_with_failed_response(): void {
        $searchParams = (new SearchParams())->setTitle('InvalidMovie123XYZ');

        Http::fake([
            'www.omdbapi.com*' => Http::response(['Response' => 'False', 'Error' => 'Movie not found!'], 404),
        ]);

        $expectedResult = $this->createMock(SearchResults::class);
        
        $this->searchResultsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($expectedResult);

        $expectedResult
            ->expects($this->once())
            ->method('setSearchParams')
            ->with($searchParams)
            ->willReturnSelf();

        $mockMeta = $this->createMock(\App\Services\MovieSearch\Models\SearchMeta::class);
        $mockMeta->expects($this->once())->method('setCurrentPage')->willReturnSelf();
        $mockMeta->expects($this->once())->method('setPerPage')->willReturnSelf();

        $expectedResult
            ->expects($this->once())
            ->method('getMeta')
            ->willReturn($mockMeta);

        $expectedResult
            ->expects($this->never())
            ->method('addMovie');

        $result = $this->client->searchMovies($searchParams);

        $this->assertSame($expectedResult, $result);
    }

    public function test_search_movies_with_type_and_year_parameters(): void {
        $searchParams = (new SearchParams())
            ->setTitle('Star Wars')
            ->setType('movie')
            ->setYear(1977)
            ->setPage(2);

        $apiResponse = [
            'Search' => [
                ['Title' => 'Star Wars', 'Year' => '1977', 'imdbID' => 'tt0076759', 'Type' => 'movie', 'Poster' => 'N/A'],
            ],
            'totalResults' => '1',
            'Response' => 'True',
        ];

        Http::fake([
            'www.omdbapi.com*' => Http::response($apiResponse, 200),
        ]);

        $expectedResult = $this->createMock(SearchResults::class);
        $this->searchResultsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($expectedResult);

        $mockSearchMovie = $this->createMock(\App\Services\MovieSearch\Models\SearchMovie::class);
        $mockSearchMovie->expects($this->once())->method('setTitle')->with('Star Wars')->willReturnSelf();
        $mockSearchMovie->expects($this->once())->method('setYear')->with('1977')->willReturnSelf();
        $mockSearchMovie->expects($this->once())->method('setImdbID')->with('tt0076759')->willReturnSelf();
        $mockSearchMovie->expects($this->once())->method('setType')->with('movie')->willReturnSelf();
        $mockSearchMovie->expects($this->once())->method('setPoster')->with('N/A')->willReturnSelf();

        $this->searchMovieFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockSearchMovie);

        $expectedResult
            ->expects($this->once())
            ->method('setSearchParams')
            ->with($searchParams)
            ->willReturnSelf();

        $expectedResult
            ->expects($this->once())
            ->method('addMovie')
            ->with($mockSearchMovie)
            ->willReturnSelf();

        $mockMeta = $this->createMock(\App\Services\MovieSearch\Models\SearchMeta::class);
        $mockMeta->method('setCurrentPage')->willReturnSelf();
        $mockMeta->method('setPerPage')->willReturnSelf();
        $mockMeta->method('setTotal')->willReturnSelf();
        $mockMeta->method('setPages')->willReturnSelf();
        $mockMeta->method('getPerPage')->willReturn(10);
        $mockMeta->method('getTotal')->willReturn(1);

        $expectedResult
            ->expects($this->once())
            ->method('getMeta')
            ->willReturn($mockMeta);

        $result = $this->client->searchMovies($searchParams);

        $this->assertSame($expectedResult, $result);
    }

    public function test_search_movies_with_empty_results(): void {
        $searchParams = (new SearchParams())->setTitle('Batman');

        $apiResponse = [
            'Response' => 'True',
            'totalResults' => '0',
        ];

        Http::fake([
            'www.omdbapi.com*' => Http::response($apiResponse, 200),
        ]);

        $expectedResult = $this->createMock(SearchResults::class);
        
        $this->searchResultsFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($expectedResult);

        $expectedResult
            ->expects($this->once())
            ->method('setSearchParams')
            ->with($searchParams)
            ->willReturnSelf();

        $mockMeta = $this->createMock(\App\Services\MovieSearch\Models\SearchMeta::class);
        $mockMeta->method('setCurrentPage')->willReturnSelf();
        $mockMeta->method('setPerPage')->willReturnSelf();
        $mockMeta->method('setTotal')->willReturnSelf();
        $mockMeta->method('setPages')->willReturnSelf();
        $mockMeta->method('getPerPage')->willReturn(10);
        $mockMeta->method('getTotal')->willReturn(0);

        $expectedResult
            ->expects($this->once())
            ->method('getMeta')
            ->willReturn($mockMeta);

        $expectedResult
            ->expects($this->never())
            ->method('addMovie');

        $result = $this->client->searchMovies($searchParams);

        $this->assertSame($expectedResult, $result);
    }

    public function test_get_movie_by_id_with_partial_ratings(): void {
        $imdbId = 'tt1234567';

        $apiResponse = [
            'Title' => 'Test Movie',
            'Year' => '2024',
            'imdbID' => 'tt1234567',
            'Ratings' => [
                ['Source' => 'Internet Movie Database', 'Value' => '7.5/10'],
            ],
            'Response' => 'True',
        ];

        Http::fake([
            'www.omdbapi.com*' => Http::response($apiResponse, 200),
        ]);

        $mockMovie = $this->createMock(\App\Services\MovieSearch\Models\Movie::class);
        $mockMovie->method('setTitle')->willReturnSelf();
        $mockMovie->method('setYear')->willReturnSelf();
        $mockMovie->method('setRated')->willReturnSelf();
        $mockMovie->method('setReleased')->willReturnSelf();
        $mockMovie->method('setRuntime')->willReturnSelf();
        $mockMovie->method('setGenre')->willReturnSelf();
        $mockMovie->method('setDirector')->willReturnSelf();
        $mockMovie->method('setWriter')->willReturnSelf();
        $mockMovie->method('setActors')->willReturnSelf();
        $mockMovie->method('setPlot')->willReturnSelf();
        $mockMovie->method('setLanguage')->willReturnSelf();
        $mockMovie->method('setCountry')->willReturnSelf();
        $mockMovie->method('setAwards')->willReturnSelf();
        $mockMovie->method('setPoster')->willReturnSelf();
        $mockMovie->method('setMetascore')->willReturnSelf();
        $mockMovie->method('setImdbRating')->willReturnSelf();
        $mockMovie->method('setImdbVotes')->willReturnSelf();
        $mockMovie->method('setImdbID')->willReturnSelf();
        $mockMovie->method('setType')->willReturnSelf();
        $mockMovie->method('setDvd')->willReturnSelf();
        $mockMovie->method('setBoxOffice')->willReturnSelf();
        $mockMovie->method('setProduction')->willReturnSelf();
        $mockMovie->method('setWebsite')->willReturnSelf();
        $mockMovie->method('setResponse')->willReturnSelf();
        
        $mockMovie->expects($this->once())->method('setRatingImdb')->with('7.5/10')->willReturnSelf();
        $mockMovie->expects($this->never())->method('setRatingRottenTomatoes');
        $mockMovie->expects($this->never())->method('setRatingMetacritic');

        $this->movieFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($mockMovie);

        $result = $this->client->getMovieByID($imdbId);

        $this->assertSame($mockMovie, $result);
    }
}
