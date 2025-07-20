<?php

declare(strict_types=1);

namespace App\Application\UseCases\Seance\AfficherSeances;

use App\Domain\Contracts\Repositories\Seance\SeanceCriteria;
use App\Domain\Contracts\Repositories\Seance\SeanceRepositoryInterface;

final readonly class AfficherSeancesUseCase
{
    public function __construct(
        private SeanceRepositoryInterface $repository
    ) {
    }

    public function execute(AfficherSeancesRequest $request): AfficherSeancesResponse
    {
        // Use the request directly as criteria (it already has the right structure)
        $criteria = new SeanceCriteria(
            recherche: $request->recherche,
            etat: $request->etat,
            qualiteProjection: $request->qualiteProjection,
            filmId: $request->filmId,
            salleId: $request->salleId,
            dateDebut: $request->dateDebut,
            dateFin: $request->dateFin,
            heureDebut: $request->heureDebut,
            heureFin: $request->heureFin,
            avecPlacesDisponibles: $request->avecPlacesDisponibles,
            placesMinimum: $request->placesMinimum,
            page: $request->page,
            perPage: $request->perPage
        );

        // Execute Repository with pagination
        $paginatedResult = $this->repository->findPaginatedByCriteria($criteria);

        // Return Response DTO
        return new AfficherSeancesResponse(
            seances: $paginatedResult->items->toArray(),
            criteria: $criteria,
            pagination: $paginatedResult->pagination
        );
    }
}
