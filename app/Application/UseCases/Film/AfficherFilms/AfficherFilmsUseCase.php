<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\AfficherFilms;

use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;

final readonly class AfficherFilmsUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository,
        private AfficherFilmsMapper $mapper,
    ) {}

    public function execute(AfficherFilmsRequest $request): AfficherFilmsResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace si page/perPage sont spécifiés
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $paginatedResult = $this->filmRepository->findPaginatedByCriteria($criteria);

            return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
        }

        // Sinon utiliser l'ancienne méthode (pour compatibilité)
        $films = $this->filmRepository->findByCriteria($criteria);

        return $this->mapper->mapToResponse($films, $criteria);
    }
}
