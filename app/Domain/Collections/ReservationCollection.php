<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Reservation\Reservation;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Domain\ValueObjects\User\UserId;
use InvalidArgumentException;

/**
 * @extends Collection<Reservation>
 */
final class ReservationCollection extends Collection
{
    public function filterByUser(UserId $userId): self
    {
        return $this->filter(fn (Reservation $reservation) => $reservation->userId
            ->equals($userId));
    }

    public function filterBySeance(SeanceId $seanceId): self
    {
        return $this->filter(fn (Reservation $reservation) => $reservation->seanceId
            ->equals($seanceId));
    }

    public function filterByStatus(string $status): self
    {
        return $this->filter(fn (Reservation $reservation) => $reservation->statut->value === $status);
    }

    public function sortByDate(): self
    {
        $sorted = $this->items->sortBy(fn (Reservation $reservation) => $reservation->createdAt);

        // @phpstan-ignore argument.type
        return new self($sorted->values()
            ->toArray());
    }

    public function getTotalAmount(): int
    {
        return $this->items->sum(fn (Reservation $reservation) => $reservation->prixTotal
            ->getAmount());
    }

    public function getTotalSeats(): int
    {
        return $this->items->sum(fn (Reservation $reservation) => $reservation->nombrePlaces);
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Reservation) {
            throw new InvalidArgumentException('ReservationCollection ne peut contenir que des instances de Reservation');
        }
    }
}
