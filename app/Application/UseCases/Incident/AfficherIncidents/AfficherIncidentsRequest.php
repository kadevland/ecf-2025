<?php

declare(strict_types=1);

namespace App\Application\UseCases\Incident\AfficherIncidents;

/**
 * Request pour AfficherIncidentsUseCase
 */
final readonly class AfficherIncidentsRequest
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
    ) {}
}
