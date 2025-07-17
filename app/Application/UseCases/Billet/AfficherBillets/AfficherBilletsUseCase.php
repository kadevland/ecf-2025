<?php

declare(strict_types=1);

namespace App\Application\UseCases\Billet\AfficherBillets;

use App\Domain\Contracts\Repositories\Billet\BilletRepositoryInterface;

/**
 * UseCase pour afficher les billets
 */
final readonly class AfficherBilletsUseCase
{
    public function __construct(
        private BilletRepositoryInterface $billetRepository,
        private AfficherBilletsMapper $mapper,
    ) {}

    public function execute(AfficherBilletsRequest $request): AfficherBilletsResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace si page/perPage sont spécifiés
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $paginatedResult = $this->billetRepository->findPaginatedByCriteria($criteria);

            return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
        }

        // Sinon utiliser l'ancienne méthode (pour compatibilité)
        $billets = $this->billetRepository->findByCriteria($criteria);

        return $this->mapper->mapToResponse($billets, $criteria);
    }
}
