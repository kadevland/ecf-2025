<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Reservation;

use App\Domain\Enums\StatutReservation;

final class ReservationCriteria
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?string $numero = null,
        public readonly ?StatutReservation $statut = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly string $direction = 'desc',
    ) {}
}
