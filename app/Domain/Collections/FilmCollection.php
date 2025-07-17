<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Film\Film;
use InvalidArgumentException;

/**
 * @extends Collection<Film>
 */
final class FilmCollection extends Collection
{
    public function findByTitle(string $title): ?Film
    {
        return $this->find(fn (Film $film) => $film->titre === $title);
    }

    public function filterByGenre(string $genre): self
    {
        return $this->filter(fn (Film $film) => in_array($genre, $film->genres, true));
    }

    public function sortByTitle(): self
    {
        $sorted = $this->items->sortBy(fn (Film $film) => $film->titre);

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    public function sortByDuration(): self
    {
        $sorted = $this->items->sortBy(fn (Film $film) => $film->dureeMinutes);

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Film) {
            throw new InvalidArgumentException('FilmCollection ne peut contenir que des instances de Film');
        }
    }
}
