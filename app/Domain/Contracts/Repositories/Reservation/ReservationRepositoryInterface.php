<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Reservation;

use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\ReservationCollection;
use App\Domain\Entities\Reservation\Reservation;
use App\Domain\ValueObjects\Reservation\ReservationId;

interface ReservationRepositoryInterface
{
    /**
     * Récupère les réservations selon des critères
     */
    public function findByCriteria(ReservationCriteria $criteria): ReservationCollection;

    /**
     * Récupère les réservations avec pagination
     *
     * @return PaginatedCollection<Reservation>
     */
    public function findPaginatedByCriteria(ReservationCriteria $criteria): PaginatedCollection;

    /**
     * Trouve une réservation par son ID
     */
    public function findById(ReservationId $id): ?Reservation;

    /**
     * Sauvegarde une réservation
     */
    public function save(Reservation $reservation): Reservation;
}
