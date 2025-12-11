<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Models\SearchParams;

class SearchParamsFactory {
 
    public function make() {
        return new SearchParams();
    }

}