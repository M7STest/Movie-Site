<?php

namespace App\Http\Responses;

use App\Services\MovieSearch\Models\Movie;
use Illuminate\Http\JsonResponse;

class MovieResponse extends JsonResponse {

    public function createResponse(Movie $movie): self {
        $this->setData([
            'data' => [
                'title' => $movie->getTitle(),
                'year' => $movie->getYear(),
                'rated' => $movie->getRated(),
                'released' => $movie->getReleased(),
                'runtime' => $movie->getRuntime(),
                'genre' => $movie->getGenre(),
                'director' => $movie->getDirector(),
                'writer' => $movie->getWriter(),
                'actors' => $movie->getActors(),
                'plot' => $movie->getPlot(),
                'language' => $movie->getLanguage(),
                'country' => $movie->getCountry(),
                'awards' => $movie->getAwards(),
                'poster' => $movie->getPoster(),
                'ratings' => [
                    'imdb' => $movie->getRatingImdb(),
                    'rottenTomatoes' => $movie->getRatingRottenTomatoes(),
                    'metacritic' => $movie->getRatingMetacritic(),
                ],
                'metascore' => $movie->getMetascore(),
                'imdbRating' => $movie->getImdbRating(),
                'imdbVotes' => $movie->getImdbVotes(),
                'imdbID' => $movie->getImdbID(),
                'type' => $movie->getType(),
                'dvd' => $movie->getDvd(),
                'boxOffice' => $movie->getBoxOffice(),
                'production' => $movie->getProduction(),
                'website' => $movie->getWebsite(),
            ]
        ]);
        return $this;
    }

}