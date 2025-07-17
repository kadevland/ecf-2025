<?php

declare(strict_types=1);

namespace App\Application\UseCases\Incident\AfficherIncidents;

use App\Domain\Contracts\Repositories\Incident\IncidentRepositoryInterface;

/**
 * UseCase pour afficher les incidents
 */
final readonly class AfficherIncidentsUseCase
{
    public function __construct(
        private IncidentRepositoryInterface $incidentRepository,
        private AfficherIncidentsMapper $mapper,
    ) {}

    public function execute(AfficherIncidentsRequest $request): AfficherIncidentsResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace si page/perPage sont spécifiés
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $paginatedResult = $this->incidentRepository->findPaginatedByCriteria($criteria);

            return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
        }

        // Sinon utiliser l'ancienne méthode (pour compatibilité)
        $incidents = $this->incidentRepository->findByCriteria($criteria);

        return $this->mapper->mapToResponse($incidents, $criteria);
    }
}
