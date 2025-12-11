<?php

namespace App\Services\MovieSearch\OMDB;

use App\Services\MovieSearch\Factories\MovieFactory;
use App\Services\MovieSearch\Factories\SearchMovieFactory;
use App\Services\MovieSearch\Factories\SearchResultsFactory;
use App\Services\MovieSearch\Models\SearchParams;
use App\Services\MovieSearch\Models\SearchResults;
use App\Services\MovieSearch\Models\Movie;
use Illuminate\Support\Facades\Http;

class OMDBClient {

    protected string $apiUrl;
    protected string $apiKey;
    protected SearchResultsFactory $searchResultsFactory;
    protected SearchMovieFactory $searchMovieFactory;
    protected MovieFactory $movieFactory;

    public function __construct(
        SearchResultsFactory $searchResultsFactory, 
        SearchMovieFactory $searchMovieFactory,
        MovieFactory $movieFactory
    ) {
        $this->apiUrl = env('OMDB_API_URL', 'http://www.omdbapi.com');
        $this->apiKey = env('OMDB_API_KEY', '');
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchMovieFactory = $searchMovieFactory;
        $this->movieFactory = $movieFactory;
    }

    public function searchMovies(SearchParams $searchParams): SearchResults {
        $response = Http::get($this->apiUrl, $this->buildSearchQueryParams($searchParams));
        $results = $this->searchResultsFactory->make();
        $results->setSearchParams($searchParams);
        $meta = $results->getMeta();
        $meta->setCurrentPage($searchParams->getPage());
        $meta->setPerPage(10);
        
        if ($response->successful()) {
            $data = $response->json();
            if(array_key_exists('Search', $data)) {
                foreach($data['Search'] as $movieData) {
                    $movie = $this->searchMovieFactory->make();
                    $movie
                        ->setTitle($movieData['Title'] ?? '')
                        ->setYear($movieData['Year'] ?? null)
                        ->setImdbID($movieData['imdbID'] ?? null)
                        ->setType($movieData['Type'] ?? null)
                        ->setPoster($movieData['Poster'] ?? null);
                    $results->addMovie($movie);
                }
            }
            if(array_key_exists('totalResults', $data)) {
                $meta->setTotal((int)$data['totalResults']);
                $meta->setPages((int)ceil(
                    $meta->getTotal() / $meta->getPerPage()
                ));
            }
        }

        return $results;
    }

    public function getMovieByID(string $movieID): ?Movie {
        $response = Http::get($this->apiUrl, [
            'apikey' => $this->apiKey,
            'i' => $movieID,
            'r' => 'json',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $movie = $this->movieFactory->make();
            $movie
                ->setTitle($data['Title'] ?? '')
                ->setYear($data['Year'] ?? null)
                ->setRated($data['Rated'] ?? null)
                ->setReleased($data['Released'] ?? null)
                ->setRuntime($data['Runtime'] ?? null)
                ->setGenre($data['Genre'] ?? null)
                ->setDirector($data['Director'] ?? null)
                ->setWriter($data['Writer'] ?? null)
                ->setActors($data['Actors'] ?? null)
                ->setPlot($data['Plot'] ?? null)
                ->setLanguage($data['Language'] ?? null)
                ->setCountry($data['Country'] ?? null)
                ->setAwards($data['Awards'] ?? null)
                ->setPoster($data['Poster'] ?? null)
                ->setMetascore($data['Metascore'] ?? null)
                ->setImdbRating($data['imdbRating'] ?? null)
                ->setImdbVotes($data['imdbVotes'] ?? null)
                ->setImdbID($data['imdbID'] ?? null)
                ->setType($data['Type'] ?? null)
                ->setDvd($data['DVD'] ?? null)
                ->setBoxOffice($data['BoxOffice'] ?? null)
                ->setProduction($data['Production'] ?? null)
                ->setWebsite($data['Website'] ?? null)
                ->setResponse($data['Response'] ?? null);
            
            // Flatten ratings
            if (isset($data['Ratings']) && is_array($data['Ratings'])) {
                foreach ($data['Ratings'] as $rating) {
                    $source = $rating['Source'] ?? '';
                    $value = $rating['Value'] ?? null;
                    
                    if ($source === 'Internet Movie Database') {
                        $movie->setRatingImdb($value);
                    } elseif ($source === 'Rotten Tomatoes') {
                        $movie->setRatingRottenTomatoes($value);
                    } elseif ($source === 'Metacritic') {
                        $movie->setRatingMetacritic($value);
                    }
                }
            }
            
            return $movie;
        }
        return null;
    }

    protected function buildSearchQueryParams(SearchParams $searchParams): array {
        $params = [
            'apikey' => $this->apiKey,
            's' => $searchParams->getTitle(),
            'r' => 'json',
            'page' => $searchParams->getPage(),
        ];

        if ($searchParams->hasType()) {
            $params['type'] = $searchParams->getType();
        }

        if ($searchParams->hasYear()) {
            $params['y'] = $searchParams->getYear();
        }

        return $params;
    }
}