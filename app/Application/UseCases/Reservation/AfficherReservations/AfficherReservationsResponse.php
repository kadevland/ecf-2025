<?php

declare(strict_types=1);

namespace App\Application\UseCases\Reservation\AfficherReservations;

use App\Application\DTOs\PaginationInfo;

/**
 * Response pour afficher les réservations
 */
final readonly class AfficherReservationsResponse
{
    public function __construct(
        public array $reservations,
        public PaginationInfo $pagination,
    ) {}
}
