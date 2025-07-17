<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\ModifierFilm;

use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use Exception;
use InvalidArgumentException;

final readonly class ModifierFilmUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository
    ) {}

    public function execute(ModifierFilmRequest $request): ModifierFilmResponse
    {
        try {
            // 1. Vérification qu'il y a des modifications
            if (! $request->hasChanges()) {
                return ModifierFilmResponse::noChanges();
            }

            // 2. Récupération du film existant
            $film = $this->filmRepository->findById($request->filmId);
            if (! $film) {
                return ModifierFilmResponse::notFound();
            }

            // 3. Application des modifications
            $champsModifies = [];

            if ($request->titre !== null) {
                $this->validateTitre($request->titre);
                $film             = $film->withTitre($request->titre);
                $champsModifies[] = 'titre';
            }

            if ($request->synopsis !== null) {
                $this->validateSynopsis($request->synopsis);
                $film             = $film->withSynopsis($request->synopsis);
                $champsModifies[] = 'synopsis';
            }

            if ($request->dureeMinutes !== null) {
                $this->validateDuree($request->dureeMinutes);
                $film             = $film->withDureeMinutes($request->dureeMinutes);
                $champsModifies[] = 'dureeMinutes';
            }

            if ($request->categorie !== null) {
                $film             = $film->withCategorie($request->categorie);
                $champsModifies[] = 'categorie';
            }

            if ($request->realisateur !== null) {
                $film             = $film->withRealisateur($request->realisateur);
                $champsModifies[] = 'realisateur';
            }

            // TODO: Ajouter les autres champs...

            // 4. Sauvegarde des modifications
            $filmModifie = $this->filmRepository->save($film);

            // 5. Dispatch des événements domaine
            // TODO: Implémenter EventDispatcher pour FilmUpdatedEvent

            return ModifierFilmResponse::success($filmModifie, $champsModifies);

        } catch (Exception $e) {
            return ModifierFilmResponse::failure(
                'Erreur lors de la modification du film: '.$e->getMessage()
            );
        }
    }

    private function validateTitre(string $titre): void
    {
        if (empty(mb_trim($titre))) {
            throw new InvalidArgumentException('Le titre du film ne peut pas être vide');
        }
    }

    private function validateSynopsis(string $synopsis): void
    {
        if (empty(mb_trim($synopsis))) {
            throw new InvalidArgumentException('Le synopsis du film ne peut pas être vide');
        }
    }

    private function validateDuree(int $duree): void
    {
        if ($duree <= 0) {
            throw new InvalidArgumentException('La durée du film doit être positive');
        }

        if ($duree > 600) {
            throw new InvalidArgumentException('La durée du film ne peut pas dépasser 600 minutes');
        }
    }
}
