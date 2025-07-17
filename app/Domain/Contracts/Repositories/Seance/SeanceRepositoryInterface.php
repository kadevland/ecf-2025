<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Seance;

use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\SeanceCollection;
use App\Domain\Entities\Seance\Seance;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Domain\ValueObjects\Seance\SeanceId;

interface SeanceRepositoryInterface
{
    /**
     * Récupère les séances selon des critères
     */
    public function findByCriteria(SeanceCriteria $criteria): SeanceCollection;

    /**
     * Récupère les séances avec pagination
     *
     * @return PaginatedCollection<Seance>
     */
    public function findPaginatedByCriteria(SeanceCriteria $criteria): PaginatedCollection;

    /**
     * Compte les séances selon des critères
     */
    public function countByCriteria(SeanceCriteria $criteria): int;

    /**
     * Trouve une séance par son ID
     */
    public function findById(SeanceId $id): ?Seance;

    /**
     * Trouve toutes les séances d'un film
     */
    public function findByFilmId(FilmId $filmId): SeanceCollection;

    /**
     * Trouve toutes les séances d'une salle
     */
    public function findBySalleId(SalleId $salleId): SeanceCollection;

    /**
     * Trouve toutes les séances d'un cinéma
     */
    public function findByCinemaId(CinemaId $cinemaId): SeanceCollection;

    /**
     * Trouve toutes les séances
     */
    public function findAll(): SeanceCollection;

    /**
     * Sauvegarde une séance (création ou mise à jour)
     */
    public function save(Seance $seance): Seance;

    /**
     * Supprime une séance par son ID
     */
    public function delete(SeanceId $id): bool;

    /**
     * Vérifie si une séance existe
     */
    public function exists(SeanceId $id): bool;

    /**
     * Charge les relations d'une séance (film, salle, réservations)
     */
    public function loadFilm(Seance $seance): Seance;

    public function loadSalle(Seance $seance): Seance;

    public function loadReservations(Seance $seance): Seance;
}
