<?php

namespace App\Http\Responses;

use App\Services\MovieSearch\Models\Movie;
use Illuminate\Http\JsonResponse;

class LastMoviesResponse extends JsonResponse {

    /**
     * @param Movie[] $movie
     * @return self
     */
    public function createResponse(array $movie): self {
        $movieData = [];
        foreach ($movie as $key => $value) {
            $movieData[] = [
                'title' => $value->getTitle(),
                'year' => $value->getYear(),
                'id' => $value->getImdbID(),
                'type' => $value->getType(),
                'poster' => $value->getPoster(),
            ];
        }
        $this->setData([
            'data' => $movieData,
            'meta' => [
                'count' => count($movieData)
            ]
        ]);
        return $this;
    }

}
