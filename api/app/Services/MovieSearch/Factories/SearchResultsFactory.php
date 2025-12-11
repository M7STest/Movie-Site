<?php 

namespace App\Services\MovieSearch\Factories;

use App\Services\MovieSearch\Models\SearchResults;

class SearchResultsFactory {

    protected SearchParamsFactory $searchParamsFactory;
    protected SearchMetaFactory $searchMetaFactory;
    protected SearchMovieFactory $searchMovieFactory;

    public function __construct(
        SearchParamsFactory $searchParamsFactory, 
        SearchMetaFactory $searchMetaFactory, 
        SearchMovieFactory $searchMovieFactory
    ) {
        $this->searchParamsFactory = $searchParamsFactory;
        $this->searchMetaFactory = $searchMetaFactory;
        $this->searchMovieFactory = $searchMovieFactory;
    }

    public function make() {
        return new SearchResults(
            $this->searchParamsFactory, 
            $this->searchMetaFactory, 
            $this->searchMovieFactory
        );
    }

}