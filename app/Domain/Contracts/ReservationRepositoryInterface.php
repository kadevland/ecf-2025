<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Criterias\ReservationCriteria;
use App\Domain\Entities\Reservation\Reservation;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Infrastructure\Collections\PaginatedCollection;

interface ReservationRepositoryInterface
{
    public function findById(ReservationId $id): ?Reservation;

    public function findPaginatedByCriteria(ReservationCriteria $criteria): PaginatedCollection;

    public function save(Reservation $reservation): Reservation;

    public function delete(ReservationId $id): bool;
}
