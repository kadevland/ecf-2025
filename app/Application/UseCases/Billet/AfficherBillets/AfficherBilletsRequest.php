<?php

declare(strict_types=1);

namespace App\Application\UseCases\Billet\AfficherBillets;

use App\Domain\Enums\TypeTarif;

/**
 * Request pour AfficherBilletsUseCase
 */
final readonly class AfficherBilletsRequest
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?string $reservationId = null,
        public readonly ?string $seanceId = null,
        public readonly ?TypeTarif $typeTarif = null,
        public readonly ?bool $utilise = null,
        public readonly ?string $dateUtilisationFrom = null,
        public readonly ?string $dateUtilisationTo = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDirection = null,
    ) {}
}
