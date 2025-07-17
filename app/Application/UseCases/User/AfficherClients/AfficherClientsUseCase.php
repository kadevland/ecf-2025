<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\AfficherClients;

use App\Domain\Contracts\Repositories\User\ClientRepositoryInterface;

/**
 * UseCase pour afficher les clients
 */
final readonly class AfficherClientsUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private AfficherClientsMapper $mapper,
    ) {}

    public function execute(AfficherClientsRequest $request): AfficherClientsResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        $paginatedClients = $this->clientRepository->findPaginatedByCriteria($criteria);

        return new AfficherClientsResponse($paginatedClients);
    }
}
