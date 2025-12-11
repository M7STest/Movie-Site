<?php

namespace App\Services\MovieSearch;

use App\Services\MovieSearch\Cache\CacheManager;
use App\Services\MovieSearch\Cache\EmptyCache;
use App\Services\MovieSearch\Factories\EmptyCacheFactory;
use App\Services\MovieSearch\Factories\SearchParamsFactory;
use App\Services\MovieSearch\Models\SearchParams;
use App\Services\MovieSearch\Models\SearchResults;
use App\Services\MovieSearch\OMDB\OMDBClient;
use App\Services\MovieSearch\Models\Movie;
use App\Services\MovieSearch\Queue\LastViewedMovieQueue;

class MovieEngine {

    protected CacheManager $cacheManager;
    protected OMDBClient $omdbClient;
    protected SearchParamsFactory $searchParamsFactory;
    protected EmptyCacheFactory $emptyCacheFactory;
    protected LastViewedMovieQueue $lastViewedMovieQueue;

    public function __construct(
        OMDBClient $omdbClient, 
        CacheManager $cacheManager,
        SearchParamsFactory $searchParamsFactory,
        EmptyCacheFactory $emptyCacheFactory,
        LastViewedMovieQueue $lastViewedMovieQueue
    ) {
        $this->omdbClient = $omdbClient;
        $this->cacheManager = $cacheManager;
        $this->searchParamsFactory = $searchParamsFactory;
        $this->emptyCacheFactory = $emptyCacheFactory;
        $this->lastViewedMovieQueue = $lastViewedMovieQueue;
    }

    public function searchMovie(string $title, ?string $type = null, ?int $year = null, ?int $page): SearchResults {
        $params = $this->createSearchParams($title, $type, $year, $page);
        $cachedResults = $this->cacheManager->get(SearchResults::buildCacheKey((string) $params));
        if ($cachedResults instanceof SearchResults) {
            return $cachedResults;
        }
        
        $results = $this->omdbClient->searchMovies($params);
        $this->cacheManager->store($results);
        return $results;
    }

    public function getMovie($movieId): ?Movie {
        $cachedMovie = $this->cacheManager->get(Movie::buildCacheKey($movieId));
        if ($cachedMovie instanceof EmptyCache) {
            return null;
        } elseif ($cachedMovie instanceof Movie) {
            $this->storeLastViewedMovie($cachedMovie);
            return $cachedMovie;
        }

        $result = $this->omdbClient->getMovieByID($movieId);
        if ($result instanceof Movie) {
            $this->cacheManager->store($result);
            $this->storeLastViewedMovie($result);
            return $result;
        }

        $this->cacheManager->store($this->emptyCacheFactory->make()
            ->setKey(Movie::buildCacheKey($movieId))
        );

        return null;
    }

    /**
     * @return Movie[]
     */
    public function getLastViewedMovies(): array {
        $ids = $this->lastViewedMovieQueue->getRecentMovies();
        $movies = [];
        foreach ($ids as $id) {
            $movie = $this->getMovie($id);
            if ($movie instanceof Movie) {
                $movies[] = $movie;
            }
        }
        return $movies;
    }

    protected function storeLastViewedMovie(Movie $movie): void {
        $this->lastViewedMovieQueue->add($movie);
    }

    protected function createSearchParams(string $title, ?string $type, ?int $year, ?int $page): SearchParams {
        return $this->searchParamsFactory->make()
            ->setTitle($title)
            ->setType($type)
            ->setYear($year)
            ->setPage($page);
    }

}