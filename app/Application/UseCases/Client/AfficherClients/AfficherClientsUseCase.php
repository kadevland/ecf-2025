<?php

declare(strict_types=1);

namespace App\Application\UseCases\Client\AfficherClients;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Contracts\Repositories\User\UserCriteria;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;

/**
 * UseCase pour afficher la liste des clients
 */
final readonly class AfficherClientsUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(AfficherClientsRequest $request): AfficherClientsResponse
    {
        $criteria = UserCriteria::create();

        // Filtrer uniquement les clients
        $criteria = $criteria->seulementClients();

        // Appliquer les filtres
        if ($request->status) {
            $criteria = $criteria->withStatus($request->status);
        }

        if ($request->search) {
            $criteria = $criteria->withSearch($request->search);
        }

        // Tri
        $criteria = $criteria->withSort($request->sortBy, $request->sortDirection);

        // Pagination
        $criteria = $criteria->withPagination($request->limit, $request->offset);

        // Récupérer les clients et le total
        $clients = $this->userRepository->findByCriteria($criteria);
        $total   = $this->userRepository->countByCriteria($criteria);

        // Créer la pagination
        $pagination = PaginationInfo::fromParams($total, $request->limit, $request->offset);

        return new AfficherClientsResponse(
            clients: $clients,
            pagination: $pagination
        );
    }
}
