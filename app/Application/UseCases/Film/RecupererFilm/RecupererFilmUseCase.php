<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererFilm;

use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use Exception;

final readonly class RecupererFilmUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository
    ) {}

    public function execute(RecupererFilmRequest $request): RecupererFilmResponse
    {
        try {
            // 1. Recherche du film par ID
            $film = $this->filmRepository->findById($request->filmId);

            if (! $film) {
                return RecupererFilmResponse::notFound(
                    "Film avec l'ID {$request->filmId->value} non trouvé"
                );
            }

            // 2. Chargement des données complémentaires si demandées
            if ($request->avecSeances) {
                // TODO: Charger les séances du film
                // $film = $this->filmRepository->loadSeances($film);
            }

            if ($request->avecImages) {
                // TODO: Charger les images du film
                // $film = $this->filmRepository->loadImages($film);
            }

            if ($request->avecRevues) {
                // TODO: Charger les revues de presse
                // $film = $this->filmRepository->loadRevues($film);
            }

            return RecupererFilmResponse::success($film);

        } catch (Exception $e) {
            return RecupererFilmResponse::failure(
                'Erreur lors de la récupération du film: '.$e->getMessage()
            );
        }
    }
}
