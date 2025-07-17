<?php

declare(strict_types=1);

namespace App\Domain\Criterias;

use App\Domain\Enums\StatutReservation;

final readonly class ReservationCriteria
{
    public function __construct(
        public readonly string $recherche = '',
        public readonly ?StatutReservation $statut = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $sort = null,
        public readonly string $direction = 'desc',
    ) {}

    public static function fromRequest(object $request): self
    {
        return new self(
            recherche: $request->recherche ?? '',
            statut: ! empty($request->statut) ? StatutReservation::from($request->statut) : null,
            perPage: $request->perPage     ?? 15,
            page: $request->page           ?? 1,
            sort: $request->sort           ?? null,
            direction: $request->direction ?? 'desc',
        );
    }

    public function hasFilters(): bool
    {
        return $this->recherche !== '' || $this->statut !== null;
    }
}
