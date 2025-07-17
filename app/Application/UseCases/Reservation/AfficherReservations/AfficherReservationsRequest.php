<?php

declare(strict_types=1);

namespace App\Application\UseCases\Reservation\AfficherReservations;

use App\Domain\Enums\StatutReservation;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\User\UserId;

/**
 * Request pour afficher les réservations
 */
final readonly class AfficherReservationsRequest
{
    public function __construct(
        public ?StatutReservation $statut = null,
        public ?UserId $userId = null,
        public ?CinemaId $cinemaId = null,
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}
