<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Application\DTOs\PaginationInfo;

/**
 * @template T
 */
final readonly class PaginatedCollection
{
    /**
     * @param  Collection<T>  $items
     */
    public function __construct(
        public Collection $items,
        public PaginationInfo $pagination,
    ) {}

    /**
     * @template U
     *
     * @param  Collection<U>  $items
     * @return PaginatedCollection<U>
     */
    public static function create(Collection $items, PaginationInfo $pagination): self
    {
        return new self($items, $pagination);
    }

    /**
     * @return T|null
     */
    public function first(): mixed
    {
        return $this->items->first();
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->items->toArray();
    }

    /**
     * Total d'éléments (pas seulement la page courante)
     */
    public function total(): int
    {
        return $this->pagination->total;
    }

    public function hasPages(): bool
    {
        return $this->pagination->hasPages();
    }

    public function currentPage(): int
    {
        return $this->pagination->currentPage ?? 1;
    }

    /**
     * @param  callable(T): bool  $callback
     * @return Collection<T>
     */
    public function filter(callable $callback): Collection
    {
        return $this->items->filter($callback);
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $callback
     * @return \Illuminate\Support\Collection<int, U>
     */
    public function map(callable $callback): \Illuminate\Support\Collection
    {
        return $this->items->map($callback);
    }
}
