<?php

declare(strict_types=1);

namespace App\Application\UseCases\Incident\AfficherIncidents;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\IncidentCollection;
use App\Domain\Contracts\Repositories\Incident\IncidentCriteria;

/**
 * Response pour AfficherIncidentsUseCase
 */
final readonly class AfficherIncidentsResponse
{
    public function __construct(
        public IncidentCollection $incidents,
        public ?IncidentCriteria $criteria = null,
        public ?PaginationInfo $pagination = null,
    ) {}
}
