<?php

declare(strict_types=1);

namespace App\Application\DTOs\Reservation;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\ReservationCollection;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;

final readonly class AfficherReservationsResponse
{
    public function __construct(
        public readonly ReservationCollection $reservations,
        public readonly PaginationInfo $pagination,
        public readonly ReservationCriteria $searchCriteria,
    ) {}
}
