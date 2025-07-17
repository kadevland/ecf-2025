<?php

declare(strict_types=1);

namespace App\Application\DTOs\Reservation;

final readonly class AfficherReservationsRequest
{
    public function __construct(
        public readonly string $recherche = '',
        public readonly ?string $statut = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $sort = null,
        public readonly string $direction = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recherche: $data['recherche'] ?? '',
            statut: $data['statut']       ?? null,
            perPage: $data['perPage']     ?? 15,
            page: $data['page']           ?? 1,
            sort: $data['sort']           ?? null,
            direction: $data['direction'] ?? 'desc',
        );
    }
}
