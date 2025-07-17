<?php

declare(strict_types=1);

namespace App\Application\UseCases\Cinema\AfficherCinemas;

use App\Domain\Contracts\Repositories\Cinema\CinemaRepositoryInterface;

final readonly class AfficherCinemasUseCase
{
    public function __construct(
        private CinemaRepositoryInterface $cinemaRepository,
        private AfficherCinemasMapper $mapper,
    ) {}

    public function execute(AfficherCinemasRequest $request): AfficherCinemasResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace si page/perPage sont spécifiés
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $paginatedResult = $this->cinemaRepository->findPaginatedByCriteria($criteria);

            return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
        }

        // Sinon utiliser l'ancienne méthode (pour compatibilité)
        $cinemas = $this->cinemaRepository->findByCriteria($criteria);

        return $this->mapper->mapToResponse($cinemas, $criteria);
    }
}
