<?php

namespace App\Http\Responses;

use App\Services\MovieSearch\Models\SearchResults;
use Illuminate\Http\JsonResponse;

class SearchMovieResponse extends JsonResponse {

    public function createResponse(SearchResults $movieData): self {
        $this->setData([
            'data' => array_map(function($movie) {
                return [
                    'id'=> $movie->getImdbID(),
                    'title' => $movie->getTitle(),
                    'year' => $movie->getYear(),
                    'type' => $movie->getType(),
                    'poster' => $movie->getPoster(),
                ];
            }, $movieData->getMovies()),
            'meta' => [
                'total' => $movieData->getMeta()->getTotal(),
                'perPage' => $movieData->getMeta()->getPerPage(),
                'pages' => $movieData->getMeta()->getPages(),
                'currentPage' => $movieData->getMeta()->getCurrentPage(),
            ]
        ]);
        return $this;
    }

}