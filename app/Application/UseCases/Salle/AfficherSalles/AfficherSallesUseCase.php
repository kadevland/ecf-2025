<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\AfficherSalles;

use App\Domain\Contracts\Repositories\Salle\SalleRepositoryInterface;

final readonly class AfficherSallesUseCase
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private AfficherSallesMapper $mapper,
    ) {}

    public function execute(AfficherSallesRequest $request): AfficherSallesResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace si page/perPage sont spécifiés
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $paginatedResult = $this->salleRepository->findPaginatedByCriteria($criteria);

            return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
        }

        // Sinon utiliser l'ancienne méthode (pour compatibilité)
        $salles = $this->salleRepository->findByCriteria($criteria);

        return $this->mapper->mapToResponse($salles, $criteria);
    }
}
