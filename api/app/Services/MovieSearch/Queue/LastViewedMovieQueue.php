<?php

namespace App\Services\MovieSearch\Queue;

use App\Services\MovieSearch\Models\Movie;
use Illuminate\Support\Facades\Redis;

class LastViewedMovieQueue {

    const QUEUE_NAME = 'last_viewed_movies';
    const QUEUE_LIMIT = 5;

    /**
     * @param Movie $movie
     * @return void
     */
    public function add(Movie $movie): void {
        $redis = Redis::connection();
        $redis->lrem(self::QUEUE_NAME, 0, $movie->getImdbID());
        $redis->lpush(self::QUEUE_NAME, $movie->getImdbID());
        $redis->ltrim(self::QUEUE_NAME, 0, self::QUEUE_LIMIT - 1);
    }

    /**
     * @return string[]
     */
    public function getRecentMovies(): array {
        $redis = Redis::connection();
        return $redis->lrange(self::QUEUE_NAME, 0, self::QUEUE_LIMIT - 1);
    }

}