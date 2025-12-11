<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchMovieRequest;
use App\Http\Responses\LastMoviesResponse;
use App\Http\Responses\MovieResponse;
use App\Http\Responses\SearchMovieResponse;
use App\Services\MovieSearch\MovieEngine;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller {
    
    private MovieEngine $movieEngine;

    public function __construct(MovieEngine $movieEngine) {
        $this->movieEngine = $movieEngine;
    }

    public function search(SearchMovieRequest $request, SearchMovieResponse $response): JsonResponse {
        return $response->createResponse($this->movieEngine->searchMovie(
            $request->get('title'), 
            $request->get('type', null), 
            $request->get('year', null), 
            $request->get('page', null)
        ));
    }

    public function get(string $imdb_id, MovieResponse $response): JsonResponse {
        if (!$imdb_id) {
            return response()->json(['error' => 'IMDb ID is required'], 400);
        }
        $movie = $this->movieEngine->getMovie($imdb_id);
        if (!$movie) {
            return response()->json(['error' => 'Movie not found'], 404);
        }
        return $response->createResponse($movie);
    }

    public function recent(LastMoviesResponse $response): JsonResponse {
        return $response->createResponse($this->movieEngine->getLastViewedMovies());
    }

}
