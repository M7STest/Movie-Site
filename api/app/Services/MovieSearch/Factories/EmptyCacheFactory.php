<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Cache\EmptyCache;

class EmptyCacheFactory {
 
    public function make() {
        return new EmptyCache();
    }

}