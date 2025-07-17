<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Incident;

/**
 * Critères de recherche pour les incidents
 */
final readonly class IncidentCriteria
{
    public function __construct(
        public ?string $recherche = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $sortBy = null,
        public ?string $sortDirection = null,
    ) {}
}
