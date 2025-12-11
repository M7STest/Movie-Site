<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Models\Movie;

class MovieFactory {
 
    public function make() {
        return new Movie();
    }

}