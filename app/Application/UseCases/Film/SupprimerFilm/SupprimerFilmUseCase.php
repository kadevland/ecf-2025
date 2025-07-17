<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\SupprimerFilm;

use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use Exception;

final readonly class SupprimerFilmUseCase
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository,
        // TODO: Ajouter SeanceRepositoryInterface pour vérifier les dépendances
        // private SeanceRepositoryInterface $seanceRepository,
    ) {}

    public function execute(SupprimerFilmRequest $request): SupprimerFilmResponse
    {
        try {
            // 1. Vérifier que le film existe
            $film = $this->filmRepository->findById($request->filmId);
            if (! $film) {
                return SupprimerFilmResponse::notFound($request->filmId);
            }

            // 2. Vérifier les dépendances (séances, réservations, etc.)
            if (! $request->forceSupprimer) {
                $dependances = $this->checkDependencies($request->filmId);
                if (! empty($dependances)) {
                    return SupprimerFilmResponse::hasDependencies(
                        $request->filmId,
                        $dependances
                    );
                }
            }

            // 3. Suppression du film
            $this->filmRepository->delete($request->filmId);

            // 4. Dispatch des événements domaine
            // TODO: Implémenter EventDispatcher pour FilmDeletedEvent

            // 5. Log de la suppression si forcée
            if ($request->forceSupprimer) {
                // TODO: Logger la suppression forcée avec la raison
            }

            return SupprimerFilmResponse::success($request->filmId);

        } catch (Exception $e) {
            return SupprimerFilmResponse::failure(
                $request->filmId,
                'Erreur lors de la suppression du film: '.$e->getMessage()
            );
        }
    }

    /**
     * Vérifie s'il existe des dépendances qui empêchent la suppression
     */
    private function checkDependencies(FilmId $filmId): array
    {
        $dependances = [];

        // TODO: Vérifier les séances programmées
        // $seancesCount = $this->seanceRepository->countByFilmId($filmId);
        // if ($seancesCount > 0) {
        //     $dependances[] = "Séances programmées: {$seancesCount}";
        // }

        // TODO: Vérifier les réservations existantes
        // TODO: Vérifier les évaluations/notes

        return $dependances;
    }
}
