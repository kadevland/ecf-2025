<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Cinema\CinemaId;
use InvalidArgumentException;

/**
 * @extends Collection<Salle>
 */
final class SalleCollection extends Collection
{
    public function findByNumber(string $numero): ?Salle
    {
        return $this->find(fn (Salle $salle) => $salle->numero->valeur === $numero);
    }

    public function filterByCinema(CinemaId $cinemaId): self
    {
        return $this->filter(fn (Salle $salle) => $salle->cinemaId
            ->equals($cinemaId));
    }

    public function filterByCapacity(int $minCapacity): self
    {
        return $this->filter(fn (Salle $salle) => $salle->capaciteMaximale() >= $minCapacity);
    }

    public function sortByCapacity(): self
    {
        $sorted = $this->items->sortBy(fn (Salle $salle) => $salle->capaciteMaximale());

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    public function sortByNumber(): self
    {
        $sorted = $this->items->sortBy(fn (Salle $salle) => $salle->numero->valeur);

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Salle) {
            throw new InvalidArgumentException('SalleCollection ne peut contenir que des instances de Salle');
        }
    }
}
