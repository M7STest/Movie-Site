<?php

namespace App\Services\MovieSearch\Models;

use App\Services\MovieSearch\Cache\CacheableModel;
use App\Services\MovieSearch\Factories\SearchMetaFactory;
use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Factories\SearchParamsFactory;

class SearchResults extends CacheableModel {
    
    protected static string $cacheKeyPrefix = "movie_search_results";

    protected int $ttl = 3600;
    
    /**
     * @var SearchMovie[]
     */
    protected array $movies = [];

    protected SearchMeta $meta;

    protected SearchParams $searchParams;

    protected SearchParamsFactory $searchParamsFactory;

    protected SearchMetaFactory $searchMetaFactory;

    protected SearchMovieFactory $searchMovieFactory;

    public function __construct(
        SearchParamsFactory $searchParamsFactory, 
        SearchMetaFactory $searchMetaFactory,
        SearchMovieFactory $searchMovieFactory
    ) {
        $this->searchParamsFactory = $searchParamsFactory;
        $this->searchMetaFactory = $searchMetaFactory;
        $this->searchMovieFactory = $searchMovieFactory;
        $this->searchParams = $this->searchParamsFactory->make();
        $this->meta = $this->searchMetaFactory->make();
    }

    /**
     * @return SearchMovie[]
     */
    public function getMovies(): array {
        return $this->movies;
    }

    public function addMovie(SearchMovie $movie): self {
        $this->movies[] = $movie;
        return $this;
    }

    public function getMeta(): SearchMeta {
        return $this->meta;
    }

    public function setMeta(SearchMeta $meta): self {
        $this->meta = $meta;
        return $this;
    }

    public function getSearchParams(): SearchParams {
        return $this->searchParams;
    }

    public function setSearchParams(SearchParams $searchParams): self {
        $this->searchParams = $searchParams;
        return $this;
    }

    public function getCacheKey(): string {
        return (string) $this->searchParams;
    }
    
    public function getData(): array {
        return [
            'movies' => array_map(function($movie) {
                return $movie->toArray();
            }, $this->movies),
            'meta' => $this->meta->toArray(),
            'searchParams' => $this->searchParams->toArray(),
        ];
    }

    public function setData(array $data): void {
        $this->movies = array_map(function($movieData) {
            return $this->searchMovieFactory->make()->fromArray($movieData);
        }, $data['movies'] ?? []);
        $this->meta = $this->searchMetaFactory->make()->fromArray($data['meta'] ?? []);
        $this->searchParams = $this->searchParamsFactory->make()->fromArray($data['searchParams'] ?? []);
    }

    public static function getFactoryClass() {
        return \App\Services\MovieSearch\Factories\SearchResultsFactory::class;
    }

}