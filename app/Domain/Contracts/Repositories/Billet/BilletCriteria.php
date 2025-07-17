<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Billet;

use App\Domain\Enums\TypeTarif;

/**
 * Critères de recherche pour les billets
 */
final readonly class BilletCriteria
{
    public function __construct(
        public ?string $recherche = null,
        public ?string $reservationId = null,
        public ?string $seanceId = null,
        public ?TypeTarif $typeTarif = null,
        public ?bool $utilise = null,
        public ?string $dateUtilisationFrom = null,
        public ?string $dateUtilisationTo = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $sortBy = null,
        public ?string $sortDirection = null,
    ) {}
}
