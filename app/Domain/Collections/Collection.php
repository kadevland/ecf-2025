<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use Countable;
use Illuminate\Support\Collection as LaravelCollection;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
abstract class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @var LaravelCollection<int, T>
     */
    protected LaravelCollection $items;

    /**
     * @param  array<T>  $items
     */
    public function __construct(array $items = [])
    {
        $this->items = new LaravelCollection();
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param  T  $item
     */
    abstract protected function validateItem(mixed $item): void;

    /**
     * @param  T  $item
     */
    final public function add(mixed $item): void
    {
        $this->validateItem($item);
        $this->items->push($item);
    }

    /**
     * @param  T  $item
     */
    final public function remove(mixed $item): bool
    {
        $key = $this->items->search($item, true);
        if ($key !== false) {
            $this->items->forget($key);
            $this->items = $this->items->values(); // Réindexer

            return true;
        }

        return false;
    }

    /**
     * @param  T  $item
     */
    final public function contains(mixed $item): bool
    {
        return $this->items->contains($item);
    }

    final public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    final public function count(): int
    {
        return $this->items->count();
    }

    /**
     * @return array<T>
     */
    final public function toArray(): array
    {
        return $this->items->toArray();
    }

    /**
     * @return T|null
     */
    final public function first(): mixed
    {
        return $this->items->first();
    }

    /**
     * @return T|null
     */
    final public function last(): mixed
    {
        return $this->items->last();
    }

    /**
     * @param  callable(T): bool  $callback
     */
    final public function filter(callable $callback): static
    {
        $filtered = $this->items->filter($callback);

        /** @var array<T> $filteredItems */
        $filteredItems = $filtered->values()
            ->toArray();

        // @phpstan-ignore return.type
        return $this->createInstance($filteredItems);
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $callback
     * @return LaravelCollection<int, U>
     */
    final public function map(callable $callback): LaravelCollection
    {
        return $this->items->map($callback);
    }

    /**
     * @param  callable(T): bool  $callback
     * @return T|null
     */
    final public function find(callable $callback): mixed
    {
        return $this->items->first($callback);
    }

    final public function slice(int $offset, ?int $length = null): static
    {
        /** @var array<T> $slicedItems */
        $slicedItems = $this->items->slice($offset, $length)
            ->values()
            ->toArray();

        // @phpstan-ignore return.type
        return $this->createInstance($slicedItems);
    }

    /**
     * @param  Collection<T>  $other
     * @return static<T>
     */
    final public function merge(self $other): static
    {
        /** @var array<T> $mergedItems */
        $mergedItems = $this->items->merge($other->items)
            ->toArray();

        return $this->createInstance($mergedItems);
    }

    final public function getIterator(): Traversable
    {
        return $this->items->getIterator();
    }

    /**
     * @return array<T>
     */
    final public function jsonSerialize(): array
    {
        return $this->items->toArray();
    }

    /**
     * @return LaravelCollection<int, T>
     */
    final public function collection(): LaravelCollection
    {
        return $this->items;
    }

    /**
     * @param  array<T>  $items
     * @return static<T>
     */
    protected function createInstance(array $items): static
    {
        /** @phpstan-ignore new.static */
        return new static($items);
    }
}
