<?php

namespace App\Services\MovieSearch\Models;

class SearchMeta {

    protected int $total = 0;
    protected int $perPage = 0;
    protected int $pages = 0;
    protected int $currentPage = 0;

    public function getTotal(): int {
        return $this->total;
    }

    public function setTotal(int $total): self {
        $this->total = $total;
        return $this;
    }

    public function getPerPage(): int {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): self {
        $this->perPage = $perPage;
        return $this;
    }

    public function getPages(): int {
        return $this->pages;
    }

    public function setPages(int $pages): self {
        $this->pages = $pages;
        return $this;
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): self {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function toArray(): array {
        return [
            'total' => $this->total,
            'perPage' => $this->perPage,
            'pages' => $this->pages,
            'currentPage' => $this->currentPage,
        ];
    }

    public function fromArray(array $data): self {
        $this->total = $data['total'] ?? 0;
        $this->perPage = $data['perPage'] ?? 0;
        $this->pages = $data['pages'] ?? 0;
        $this->currentPage = $data['currentPage'] ?? 0;
        return $this;
    }

}