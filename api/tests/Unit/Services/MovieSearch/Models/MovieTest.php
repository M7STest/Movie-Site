<?php

namespace Tests\Unit\Services\MovieSearch\Models;

use App\Services\MovieSearch\Factories\MovieFactory;
use App\Services\MovieSearch\Models\Movie;
use Tests\TestCase;

class MovieTest extends TestCase {

    public function test_set_and_get_title(): void {
        $movie = new Movie();
        
        $result = $movie->setTitle('The Dark Knight');
        
        $this->assertSame($movie, $result);
        $this->assertEquals('The Dark Knight', $movie->getTitle());
    }

    public function test_set_and_get_year(): void {
        $movie = new Movie();
        
        $movie->setYear('2008');
        
        $this->assertEquals('2008', $movie->getYear());
    }

    public function test_set_and_get_imdb_id(): void {
        $movie = new Movie();
        
        $movie->setImdbID('tt0468569');
        
        $this->assertEquals('tt0468569', $movie->getImdbID());
    }

    public function test_get_cache_key_returns_imdb_id(): void {
        $movie = new Movie();
        $movie->setImdbID('tt1234567');
        
        $this->assertEquals('tt1234567', $movie->getCacheKey());
    }

    public function test_get_full_cache_key_with_prefix(): void {
        $movie = new Movie();
        $movie->setImdbID('tt9999999');
        
        $fullKey = $movie->getFullCacheKey();
        
        $this->assertStringStartsWith('movie_', $fullKey);
        $this->assertStringContainsString('tt9999999', $fullKey);
    }

    public function test_fluent_setters_chaining(): void {
        $movie = new Movie();
        
        $result = $movie
            ->setTitle('Inception')
            ->setYear('2010')
            ->setImdbID('tt1375666')
            ->setType('movie')
            ->setPlot('A thief who steals corporate secrets...');
        
        $this->assertSame($movie, $result);
    }

    public function test_get_data_returns_array(): void {
        $movie = new Movie();
        $movie->setTitle('Test Movie')
            ->setYear('2024')
            ->setImdbID('tt0000000');
        
        $data = $movie->getData();
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('year', $data);
        $this->assertArrayHasKey('imdbid', $data);
        $this->assertEquals('Test Movie', $data['title']);
        $this->assertEquals('2024', $data['year']);
        $this->assertEquals('tt0000000', $data['imdbid']);
    }

    public function test_set_data_populates_movie(): void {
        $movie = new Movie();
        
        $data = [
            'title' => 'Restored Movie',
            'year' => '2023',
            'imdbid' => 'tt1111111',
            'plot' => 'A restored movie plot',
            'director' => 'John Doe',
        ];
        
        $movie->setData($data);
        
        $this->assertEquals('Restored Movie', $movie->getTitle());
        $this->assertEquals('2023', $movie->getYear());
        $this->assertEquals('tt1111111', $movie->getImdbID());
        $this->assertEquals('A restored movie plot', $movie->getPlot());
        $this->assertEquals('John Doe', $movie->getDirector());
    }

    public function test_get_factory_class(): void {
        $factoryClass = Movie::getFactoryClass();
        
        $this->assertEquals(MovieFactory::class, $factoryClass);
    }

    public function test_default_ttl(): void {
        $movie = new Movie();
        
        $this->assertEquals(86400, $movie->getTtl()); // 24 hours
    }

    public function test_set_all_properties(): void {
        $movie = new Movie();
        
        $movie->setTitle('Complete Movie')
            ->setYear('2025')
            ->setRated('PG-13')
            ->setReleased('01 Jan 2025')
            ->setRuntime('120 min')
            ->setGenre('Action')
            ->setDirector('Jane Smith')
            ->setWriter('Writer Name')
            ->setActors('Actor 1, Actor 2')
            ->setPlot('A complete plot')
            ->setLanguage('English')
            ->setCountry('USA')
            ->setAwards('10 wins')
            ->setPoster('https://example.com/poster.jpg')
            ->setMetascore('85')
            ->setImdbRating('8.5')
            ->setImdbVotes('100,000')
            ->setImdbID('tt5555555')
            ->setType('movie')
            ->setDvd('N/A')
            ->setBoxOffice('$100M')
            ->setProduction('Studio')
            ->setWebsite('https://movie.com')
            ->setRatingImdb('8.5/10')
            ->setRatingRottenTomatoes('90%')
            ->setRatingMetacritic('85/100');
        
        $this->assertEquals('Complete Movie', $movie->getTitle());
        $this->assertEquals('PG-13', $movie->getRated());
        $this->assertEquals('8.5/10', $movie->getRatingImdb());
    }
}
