<?php

namespace Tests\Unit\Services\MovieSearch\Models;

use App\Services\MovieSearch\Models\SearchMovie;
use Tests\TestCase;

class SearchMovieTest extends TestCase {

    public function test_set_and_get_title(): void {
        $movie = new SearchMovie();
        
        $result = $movie->setTitle('The Matrix');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('The Matrix', $movie->getTitle());
    }

    public function test_default_title_is_empty_string(): void {
        $movie = new SearchMovie();
        
        $this->assertEquals('', $movie->getTitle());
    }

    public function test_set_and_get_year(): void {
        $movie = new SearchMovie();
        
        $result = $movie->setYear('1999');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('1999', $movie->getYear());
    }

    public function test_set_and_get_imdb_id(): void {
        $movie = new SearchMovie();
        
        $result = $movie->setImdbID('tt0133093');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('tt0133093', $movie->getImdbID());
    }

    public function test_set_and_get_type(): void {
        $movie = new SearchMovie();
        
        $result = $movie->setType('movie');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('movie', $movie->getType());
    }

    public function test_set_and_get_poster(): void {
        $movie = new SearchMovie();
        
        $result = $movie->setPoster('https://example.com/poster.jpg');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('https://example.com/poster.jpg', $movie->getPoster());
    }

    public function test_to_array(): void {
        $movie = new SearchMovie();
        $movie->setTitle('Inception')
            ->setYear('2010')
            ->setImdbID('tt1375666')
            ->setType('movie')
            ->setPoster('https://example.com/inception.jpg');
        
        $expected = [
            'title' => 'Inception',
            'year' => '2010',
            'imdbID' => 'tt1375666',
            'type' => 'movie',
            'poster' => 'https://example.com/inception.jpg',
        ];
        
        $this->assertEquals($expected, $movie->toArray());
    }

    public function test_fluent_interface_chaining(): void {
        $movie = new SearchMovie();
        
        $result = $movie
            ->setTitle('Interstellar')
            ->setYear('2014')
            ->setImdbID('tt0816692')
            ->setType('movie')
            ->setPoster('https://example.com/interstellar.jpg');
        
        $this->assertSame($movie, $result);
    }
}
