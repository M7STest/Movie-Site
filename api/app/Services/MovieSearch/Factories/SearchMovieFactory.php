<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Models\SearchMovie;

class SearchMovieFactory {
 
    public function make() {
        return new SearchMovie();
    }

}