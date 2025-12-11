<?php

namespace App\Services\MovieSearch\Cache;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\App;

class CacheManager {
    
    const DATA_KEY = 'data';
    const FACTORY_KEY = 'factory';

    public function store(?CacheableModel $model): void {
        $key = $model->getFullCacheKey();
        $data = json_encode([
            self::DATA_KEY => $model->getData(),
            self::FACTORY_KEY => $model::getFactoryClass(),
        ]);
        Redis::set($key, $data, 'EX', $model->getTtl());        
    }

    public function get(string $key): ?CacheableModel {
        $data = Redis::get($key);
        if (!empty($data)) {
            $decoded = json_decode($data, true);
            $factoryClass = $decoded[self::FACTORY_KEY];
            $factory = App::make($factoryClass);
            $model = $factory->make();
            if ($model instanceof CacheableModel) {
                $model->setData($decoded[self::DATA_KEY]);
                return $model;
            } else {
                throw new \Exception("Factory class $factoryClass does not produce a CacheableModel");
            }
        }
        return null;
    }

}