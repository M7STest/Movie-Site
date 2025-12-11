<?php

namespace App\Services\MovieSearch\Models;

use App\Services\MovieSearch\Cache\CacheableModel;

class Movie extends CacheableModel {
    
    protected static string $cacheKeyPrefix = "movie";
    protected int $ttl = 3600 * 24;
    
    protected string $title = '';
    protected ?string $year = null;
    protected ?string $rated = null;
    protected ?string $released = null;
    protected ?string $runtime = null;
    protected ?string $genre = null;
    protected ?string $director = null;
    protected ?string $writer = null;
    protected ?string $actors = null;
    protected ?string $plot = null;
    protected ?string $language = null;
    protected ?string $country = null;
    protected ?string $awards = null;
    protected ?string $poster = null;
    protected ?string $metascore = null;
    protected ?string $imdbRating = null;
    protected ?string $imdbVotes = null;
    protected ?string $imdbID = null;
    protected ?string $type = null;
    protected ?string $dvd = null;
    protected ?string $boxOffice = null;
    protected ?string $production = null;
    protected ?string $website = null;
    protected ?string $response = null;
    
    // Flattened ratings
    protected ?string $ratingImdb = null;
    protected ?string $ratingRottenTomatoes = null;
    protected ?string $ratingMetacritic = null;

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getYear(): ?string {
        return $this->year;
    }

    public function setYear(?string $year): self {
        $this->year = $year;
        return $this;
    }

    public function getImdbID(): ?string {
        return $this->imdbID;
    }

    public function setImdbID(?string $imdbID): self {
        $this->imdbID = $imdbID;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): self {
        $this->type = $type;
        return $this;
    }

    public function getPoster(): ?string {
        return $this->poster;
    }

    public function setPoster(?string $poster): self {
        $this->poster = $poster;
        return $this;
    }

    public function getRated(): ?string {
        return $this->rated;
    }

    public function setRated(?string $rated): self {
        $this->rated = $rated;
        return $this;
    }

    public function getReleased(): ?string {
        return $this->released;
    }

    public function setReleased(?string $released): self {
        $this->released = $released;
        return $this;
    }

    public function getRuntime(): ?string {
        return $this->runtime;
    }

    public function setRuntime(?string $runtime): self {
        $this->runtime = $runtime;
        return $this;
    }

    public function getGenre(): ?string {
        return $this->genre;
    }

    public function setGenre(?string $genre): self {
        $this->genre = $genre;
        return $this;
    }

    public function getDirector(): ?string {
        return $this->director;
    }

    public function setDirector(?string $director): self {
        $this->director = $director;
        return $this;
    }

    public function getWriter(): ?string {
        return $this->writer;
    }

    public function setWriter(?string $writer): self {
        $this->writer = $writer;
        return $this;
    }

    public function getActors(): ?string {
        return $this->actors;
    }

    public function setActors(?string $actors): self {
        $this->actors = $actors;
        return $this;
    }

    public function getPlot(): ?string {
        return $this->plot;
    }

    public function setPlot(?string $plot): self {
        $this->plot = $plot;
        return $this;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(?string $language): self {
        $this->language = $language;
        return $this;
    }

    public function getCountry(): ?string {
        return $this->country;
    }

    public function setCountry(?string $country): self {
        $this->country = $country;
        return $this;
    }

    public function getAwards(): ?string {
        return $this->awards;
    }

    public function setAwards(?string $awards): self {
        $this->awards = $awards;
        return $this;
    }

    public function getMetascore(): ?string {
        return $this->metascore;
    }

    public function setMetascore(?string $metascore): self {
        $this->metascore = $metascore;
        return $this;
    }

    public function getImdbRating(): ?string {
        return $this->imdbRating;
    }

    public function setImdbRating(?string $imdbRating): self {
        $this->imdbRating = $imdbRating;
        return $this;
    }

    public function getImdbVotes(): ?string {
        return $this->imdbVotes;
    }

    public function setImdbVotes(?string $imdbVotes): self {
        $this->imdbVotes = $imdbVotes;
        return $this;
    }

    public function getDvd(): ?string {
        return $this->dvd;
    }

    public function setDvd(?string $dvd): self {
        $this->dvd = $dvd;
        return $this;
    }

    public function getBoxOffice(): ?string {
        return $this->boxOffice;
    }

    public function setBoxOffice(?string $boxOffice): self {
        $this->boxOffice = $boxOffice;
        return $this;
    }

    public function getProduction(): ?string {
        return $this->production;
    }

    public function setProduction(?string $production): self {
        $this->production = $production;
        return $this;
    }

    public function getWebsite(): ?string {
        return $this->website;
    }

    public function setWebsite(?string $website): self {
        $this->website = $website;
        return $this;
    }

    public function getResponse(): ?string {
        return $this->response;
    }

    public function setResponse(?string $response): self {
        $this->response = $response;
        return $this;
    }

    public function getRatingImdb(): ?string {
        return $this->ratingImdb;
    }

    public function setRatingImdb(?string $ratingImdb): self {
        $this->ratingImdb = $ratingImdb;
        return $this;
    }

    public function getRatingRottenTomatoes(): ?string {
        return $this->ratingRottenTomatoes;
    }

    public function setRatingRottenTomatoes(?string $ratingRottenTomatoes): self {
        $this->ratingRottenTomatoes = $ratingRottenTomatoes;
        return $this;
    }

    public function getRatingMetacritic(): ?string {
        return $this->ratingMetacritic;
    }

    public function setRatingMetacritic(?string $ratingMetacritic): self {
        $this->ratingMetacritic = $ratingMetacritic;
        return $this;
    }

    public function getCacheKey(): string {
        return $this->imdbID;
    }
    
    public function getData(): array {
        return [
            'title' => $this->title,
            'year' => $this->year,
            'rated' => $this->rated,
            'released' => $this->released,
            'runtime' => $this->runtime,
            'genre' => $this->genre,
            'director' => $this->director,
            'writer' => $this->writer,
            'actors' => $this->actors,
            'plot' => $this->plot,
            'language' => $this->language,
            'country' => $this->country,
            'awards' => $this->awards,
            'poster' => $this->poster,
            'metascore' => $this->metascore,
            'imdbrating' => $this->imdbRating,
            'imdbvotes' => $this->imdbVotes,
            'imdbid' => $this->imdbID,
            'type' => $this->type,
            'dvd' => $this->dvd,
            'boxoffice' => $this->boxOffice,
            'production' => $this->production,
            'website' => $this->website,
            'response' => $this->response,
            'ratingimdb' => $this->ratingImdb,
            'ratingrottentomatoes' => $this->ratingRottenTomatoes,
            'ratingmetacritic' => $this->ratingMetacritic,
        ];
    }

    public function setData(array $data): void {
        $this->title = $data['title'] ?? '';
        $this->year = $data['year'] ?? null;
        $this->rated = $data['rated'] ?? null;
        $this->released = $data['released'] ?? null;
        $this->runtime = $data['runtime'] ?? null;
        $this->genre = $data['genre'] ?? null;
        $this->director = $data['director'] ?? null;
        $this->writer = $data['writer'] ?? null;
        $this->actors = $data['actors'] ?? null;
        $this->plot = $data['plot'] ?? null;
        $this->language = $data['language'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->awards = $data['awards'] ?? null;
        $this->poster = $data['poster'] ?? null;
        $this->metascore = $data['metascore'] ?? null;
        $this->imdbRating = $data['imdbrating'] ?? null;
        $this->imdbVotes = $data['imdbvotes'] ?? null;
        $this->imdbID = $data['imdbid'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->dvd = $data['dvd'] ?? null;
        $this->boxOffice = $data['boxoffice'] ?? null;
        $this->production = $data['production'] ?? null;
        $this->website = $data['website'] ?? null;
        $this->response = $data['response'] ?? null;
        $this->ratingImdb = $data['ratingimdb'] ?? null;
        $this->ratingRottenTomatoes = $data['ratingrottentomatoes'] ?? null;
        $this->ratingMetacritic = $data['ratingmetacritic'] ?? null;
    }

    public static function getFactoryClass() {
        return \App\Services\MovieSearch\Factories\MovieFactory::class;
    }

}