<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final readonly class PaginationInfo
{
    public function __construct(
        public int $total,
        public ?int $limit = null,
        public ?int $offset = null,
        public ?int $currentPage = null,
        public ?int $totalPages = null,
        public ?bool $hasNextPage = null,
        public ?bool $hasPrevPage = null,
    ) {}

    public static function fromParams(int $total, ?int $limit = null, ?int $offset = null): self
    {
        if ($limit === null || $offset === null) {
            return new self(total: $total);
        }

        $currentPage = (int) floor($offset / $limit) + 1;
        $totalPages  = (int) ceil($total / $limit);

        return new self(
            total: $total,
            limit: $limit,
            offset: $offset,
            currentPage: $currentPage,
            totalPages: $totalPages,
            hasNextPage: $currentPage < $totalPages,
            hasPrevPage: $currentPage > 1,
        );
    }

    public static function fromPageParams(int $total, ?int $page = null, ?int $perPage = null): self
    {
        if ($page === null || $perPage === null) {
            return new self(total: $total);
        }

        $offset     = ($page - 1) * $perPage;
        $totalPages = (int) ceil($total / $perPage);

        return new self(
            total: $total,
            limit: $perPage,
            offset: $offset,
            currentPage: $page,
            totalPages: $totalPages,
            hasNextPage: $page < $totalPages,
            hasPrevPage: $page > 1,
        );
    }

    public function firstItem(): int
    {
        return $this->offset ? $this->offset + 1 : 1;
    }

    public function lastItem(): int
    {
        if ($this->offset === null || $this->limit === null) {
            return $this->total;
        }

        return min($this->offset + $this->limit, $this->total);
    }

    public function hasPages(): bool
    {
        return $this->totalPages !== null && $this->totalPages > 1;
    }
}
