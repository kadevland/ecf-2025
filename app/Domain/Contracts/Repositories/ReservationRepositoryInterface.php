<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Domain\Collections\ReservationCollection;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;
use App\Domain\Entities\Reservation\Reservation;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Domain\ValueObjects\User\UserId;

interface ReservationRepositoryInterface
{
    /**
     * Récupère les réservations selon des critères
     */
    public function findByCriteria(ReservationCriteria $criteria): ReservationCollection;

    /**
     * Compte les réservations selon des critères
     */
    public function countByCriteria(ReservationCriteria $criteria): int;

    /**
     * Trouve une réservation par son ID
     */
    public function findById(ReservationId $id): ?Reservation;

    /**
     * Trouve toutes les réservations d'un utilisateur
     */
    public function findByUserId(UserId $userId): ReservationCollection;

    /**
     * Trouve toutes les réservations d'une séance
     */
    public function findBySeanceId(SeanceId $seanceId): ReservationCollection;

    /**
     * Trouve toutes les réservations
     */
    public function findAll(): ReservationCollection;

    /**
     * Sauvegarde une réservation (création ou mise à jour)
     */
    public function save(Reservation $reservation): Reservation;

    /**
     * Supprime une réservation par son ID
     */
    public function delete(ReservationId $id): bool;

    /**
     * Vérifie si une réservation existe
     */
    public function exists(ReservationId $id): bool;

    /**
     * Charge les relations d'une réservation (billets, utilisateur, séance)
     */
    public function loadBillets(Reservation $reservation): Reservation;

    public function loadUser(Reservation $reservation): Reservation;

    public function loadSeance(Reservation $reservation): Reservation;
}
