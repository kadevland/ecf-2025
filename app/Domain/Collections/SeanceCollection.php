<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Seance\Seance;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * @extends Collection<Seance>
 */
final class SeanceCollection extends Collection
{
    public function filterByFilm(FilmId $filmId): self
    {
        return $this->filter(fn (Seance $seance) => $seance->filmId
            ->equals($filmId));
    }

    public function filterBySalle(SalleId $salleId): self
    {
        return $this->filter(fn (Seance $seance) => $seance->salleId
            ->equals($salleId));
    }

    public function filterByDate(DateTimeImmutable $date): self
    {
        return $this->filter(fn (Seance $seance) => $seance->seanceHoraire->debut()
            ->format('Y-m-d') === $date->format('Y-m-d'));
    }

    public function filterByDateRange(DateTimeImmutable $start, DateTimeImmutable $end): self
    {
        return $this->filter(
            fn (Seance $seance) => $seance->seanceHoraire->debut() >= $start && $seance->seanceHoraire->debut() <= $end
        );
    }

    public function filterAvailable(): self
    {
        return $this->filter(fn (Seance $seance) => $seance->nombrePlacesDisponibles > 0);
    }

    public function sortByDateTime(): self
    {
        $sorted = $this->items->sortBy(fn (Seance $seance) => $seance->seanceHoraire->debut());

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    /**
     * @return array<string, array<Seance>>
     */
    public function groupByDate(): array
    {
        // @phpstan-ignore return.type
        return $this->items->groupBy(fn (Seance $seance) => $seance->seanceHoraire->debut()
            ->format('Y-m-d'))
            ->toArray();
    }

    public function getTotalAvailableSeats(): int
    {
        return $this->items->sum(fn (Seance $seance) => $seance->nombrePlacesDisponibles);
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Seance) {
            throw new InvalidArgumentException('SeanceCollection ne peut contenir que des instances de Seance');
        }
    }
}
