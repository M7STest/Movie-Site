<?php

namespace App\Services\MovieSearch\Models;

class SearchMovie {

    protected string $title = '';
    protected ?string $year;
    protected ?string $imdbID;
    protected ?string $type;
    protected ?string $poster;

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

    public function toArray(): array {
        return [
            'title' => $this->title,
            'year' => $this->year,
            'imdbID' => $this->imdbID,
            'type' => $this->type,
            'poster' => $this->poster,
        ];
    }

    public function fromArray(array $data): self {
        $this->title = $data['title'] ?? '';
        $this->year = $data['year'] ?? null;
        $this->imdbID = $data['imdbID'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->poster = $data['poster'] ?? null;
        return $this;
    }

}