<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererListeFilms;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Contracts\Repositories\Film\FilmCriteria;
use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use Exception;

final readonly class RecupererListeFilmsUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository
    ) {}

    public function execute(RecupererListeFilmsRequest $request): RecupererListeFilmsResponse
    {
        try {
            // 1. Construction des critères de recherche
            $criteria = new FilmCriteria(
                categorie: $request->categorie,
                recherche: $request->recherche,
                realisateur: $request->realisateur,
                dureeMin: $request->dureeMin,
                dureeMax: $request->dureeMax,
                sortBy: $request->sortBy,
                sortDirection: $request->sortDirection,
                limit: $request->limit,
                offset: $request->offset,
                seulementAffiche: $request->seulementAffiche,
                avecSeances: $request->avecSeances,
            );

            // 2. Récupération des films selon les critères
            $films = $this->filmRepository->findByCriteria($criteria);

            // 3. Comptage total pour la pagination
            $total = $this->filmRepository->countByCriteria($criteria);

            // 4. Création des informations de pagination
            $pagination = PaginationInfo::fromParams(
                total: $total,
                limit: $request->limit,
                offset: $request->offset
            );

            return RecupererListeFilmsResponse::success($films, $pagination);

        } catch (Exception $e) {
            return RecupererListeFilmsResponse::failure(
                'Erreur lors de la récupération de la liste des films: '.$e->getMessage()
            );
        }
    }
}
