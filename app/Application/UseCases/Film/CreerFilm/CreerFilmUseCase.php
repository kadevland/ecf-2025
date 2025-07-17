<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\CreerFilm;

use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use App\Domain\Entities\Film\Film;
use App\Domain\ValueObjects\Film\FilmId;
use Exception;
use InvalidArgumentException;

final readonly class CreerFilmUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository
    ) {}

    public function execute(CreerFilmRequest $request): CreerFilmResponse
    {
        try {
            // 1. Validation des données métier
            $this->validateRequest($request);

            // 2. Création de l'entité Film
            $film = new Film(
                id: FilmId::generate(),
                titre: $request->titre,
                synopsis: $request->synopsis,
                dureeMinutes: $request->dureeMinutes,
                categorie: $request->categorie,
                realisateur: $request->realisateur,
                acteursPrincipaux: $request->acteursPrincipaux ?? [],
                datesSortie: $request->datesSortie,
                qualitesProjection: $request->qualitesProjection ?? [],
                trailerUrl: $request->trailerUrl,
            );

            // 3. Sauvegarde en base
            $savedFilm = $this->filmRepository->save($film);

            // 4. Dispatch des événements domaine
            // TODO: Implémenter EventDispatcher

            return CreerFilmResponse::success($savedFilm);

        } catch (Exception $e) {
            return CreerFilmResponse::failure(
                'Erreur lors de la création du film: '.$e->getMessage()
            );
        }
    }

    private function validateRequest(CreerFilmRequest $request): void
    {
        if (empty(mb_trim($request->titre))) {
            throw new InvalidArgumentException('Le titre du film est obligatoire');
        }

        if (empty(mb_trim($request->synopsis))) {
            throw new InvalidArgumentException('Le synopsis du film est obligatoire');
        }

        if ($request->dureeMinutes <= 0) {
            throw new InvalidArgumentException('La durée du film doit être positive');
        }

        if ($request->dureeMinutes > 600) { // 10 heures max
            throw new InvalidArgumentException('La durée du film ne peut pas dépasser 600 minutes');
        }
    }
}
