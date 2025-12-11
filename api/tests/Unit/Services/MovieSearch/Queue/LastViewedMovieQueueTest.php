<?php

namespace Tests\Unit\Services\MovieSearch\Queue;

use App\Services\MovieSearch\Models\Movie;
use App\Services\MovieSearch\Queue\LastViewedMovieQueue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class LastViewedMovieQueueTest extends TestCase {

    private LastViewedMovieQueue $queue;

    protected function setUp(): void {
        parent::setUp();
        
        $this->queue = new LastViewedMovieQueue();
    }

    public function test_add_movie_to_empty_queue(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getImdbID')->willReturn('tt0468569');

        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrem')
            ->once()
            ->with('last_viewed_movies', 0, 'tt0468569')
            ->andReturn(0);
        
        Redis::shouldReceive('lpush')
            ->once()
            ->with('last_viewed_movies', 'tt0468569')
            ->andReturn(1);
        
        Redis::shouldReceive('ltrim')
            ->once()
            ->with('last_viewed_movies', 0, 4);

        $this->queue->add($movie);
    }

    public function test_add_movie_removes_duplicate_before_adding(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getImdbID')->willReturn('tt1234567');

        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrem')
            ->once()
            ->with('last_viewed_movies', 0, 'tt1234567')
            ->andReturn(1);
        
        Redis::shouldReceive('lpush')
            ->once()
            ->with('last_viewed_movies', 'tt1234567')
            ->andReturn(1);
        
        Redis::shouldReceive('ltrim')
            ->once()
            ->with('last_viewed_movies', 0, 4);

        $this->queue->add($movie);
    }

    public function test_add_movie_maintains_queue_limit(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getImdbID')->willReturn('tt9999999');

        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrem')
            ->once()
            ->andReturn(0);
        
        Redis::shouldReceive('lpush')
            ->once()
            ->andReturn(6);
        
        Redis::shouldReceive('ltrim')
            ->once()
            ->with('last_viewed_movies', 0, 4);

        $this->queue->add($movie);
    }

    public function test_get_recent_movies_returns_array(): void {
        $expectedMovies = ['tt0468569', 'tt1375666', 'tt0816692', 'tt0137523', 'tt0109830'];

        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrange')
            ->once()
            ->with('last_viewed_movies', 0, 4)
            ->andReturn($expectedMovies);

        $result = $this->queue->getRecentMovies();

        $this->assertIsArray($result);
        $this->assertCount(5, $result);
        $this->assertEquals($expectedMovies, $result);
    }

    public function test_get_recent_movies_returns_empty_array_when_queue_is_empty(): void {
        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrange')
            ->once()
            ->with('last_viewed_movies', 0, 4)
            ->andReturn([]);

        $result = $this->queue->getRecentMovies();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_recent_movies_respects_queue_limit(): void {
        Redis::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        Redis::shouldReceive('lrange')
            ->once()
            ->with('last_viewed_movies', 0, 4)
            ->andReturn(['tt1', 'tt2', 'tt3']);

        $result = $this->queue->getRecentMovies();

        $this->assertCount(3, $result);
    }

    public function test_queue_constants(): void {
        $this->assertEquals('last_viewed_movies', LastViewedMovieQueue::QUEUE_NAME);
        $this->assertEquals(5, LastViewedMovieQueue::QUEUE_LIMIT);
    }

    public function test_add_multiple_movies_in_sequence(): void {
        $movie1 = $this->createMock(Movie::class);
        $movie1->method('getImdbID')->willReturn('tt0001');
        
        $movie2 = $this->createMock(Movie::class);
        $movie2->method('getImdbID')->willReturn('tt0002');
        
        $movie3 = $this->createMock(Movie::class);
        $movie3->method('getImdbID')->willReturn('tt0003');

        Redis::shouldReceive('connection')
            ->times(3)
            ->andReturnSelf();
        
        Redis::shouldReceive('lrem')
            ->times(3)
            ->andReturn(0);
        
        Redis::shouldReceive('lpush')
            ->times(3)
            ->andReturnUsing(function($queue, $value) {
                static $count = 0;
                return ++$count;
            });
        
        Redis::shouldReceive('ltrim')
            ->times(3)
            ->with('last_viewed_movies', 0, 4);

        $this->queue->add($movie1);
        $this->queue->add($movie2);
        $this->queue->add($movie3);
    }

    public function test_add_same_movie_twice_moves_it_to_front(): void {
        $movie = $this->createMock(Movie::class);
        $movie->method('getImdbID')->willReturn('tt5555555');

        Redis::shouldReceive('connection')
            ->times(2)
            ->andReturnSelf();
        
        Redis::shouldReceive('lrem')
            ->twice()
            ->with('last_viewed_movies', 0, 'tt5555555')
            ->andReturn(0, 1);
        
        Redis::shouldReceive('lpush')
            ->twice()
            ->with('last_viewed_movies', 'tt5555555')
            ->andReturn(1);
        
        Redis::shouldReceive('ltrim')
            ->twice()
            ->with('last_viewed_movies', 0, 4);

        $this->queue->add($movie);
        $this->queue->add($movie);
    }
}
