<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Models\SearchMeta;

class SearchMetaFactory {
 
    public function make() {
        return new SearchMeta();
    }

}