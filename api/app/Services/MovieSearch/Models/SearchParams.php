<?php

namespace App\Services\MovieSearch\Models;

class SearchParams {

    protected string $title = '';
    protected ?string $type = null;
    protected ?int $year = null;
    protected ?int $page = 1;

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function hasType(): bool {
        return !is_null($this->type);
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): self {
        $this->type = $type;
        return $this;
    }

    public function hasYear(): bool {
        return !is_null($this->year);
    }

    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(?int $year): self {
        $this->year = $year;
        return $this;
    }

    public function getPage(): int {
        return $this->page ?? 1;
    }

    public function setPage(?int $page): self {
        $this->page = $page ?? 1;
        return $this;
    }

    public function toArray(): array {
        return [
            'title' => $this->title,
            'type' => $this->type,
            'year' => $this->year,
            'page'=> $this->page,
        ];
    }

    public function fromArray(array $data): self {
        if(array_key_exists('title', $data)) {
            $this->title = $data['title'];
        }
        if(array_key_exists('type', $data)) {
            $this->type = $data['type'];
        }
        if(array_key_exists('year', $data)) {
            $this->year = $data['year'];
        }
        if(array_key_exists('page', $data)) {
            $this->page = $data['page'];
        }
        return $this;
    }

    public function __tostring(): string {
        $items = $this->toArray();
        return implode(':', $items);
    }

}