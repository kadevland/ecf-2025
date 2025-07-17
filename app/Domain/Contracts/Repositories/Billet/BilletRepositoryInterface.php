<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Billet;

use App\Domain\Collections\BilletCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Entities\Billet\Billet;
use App\Domain\ValueObjects\Billet\BilletId;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;

/**
 * Interface pour le repository des billets
 */
interface BilletRepositoryInterface
{
    /**
     * Trouver les billets selon des critères
     */
    public function findByCriteria(BilletCriteria $criteria): BilletCollection;

    /**
     * Trouver les billets avec pagination
     */
    public function findPaginatedByCriteria(BilletCriteria $criteria): PaginatedCollection;

    /**
     * Trouver un billet par son ID
     */
    public function findById(BilletId $id): ?Billet;

    /**
     * Trouver les billets d'une réservation
     */
    public function findByReservationId(ReservationId $reservationId): BilletCollection;

    /**
     * Trouver les billets d'une séance
     */
    public function findBySeanceId(SeanceId $seanceId): BilletCollection;

    /**
     * Trouver un billet par son numéro
     */
    public function findByNumeroBillet(string $numeroBillet): ?Billet;

    /**
     * Trouver un billet par son QR code
     */
    public function findByQrCode(string $qrCode): ?Billet;

    /**
     * Sauvegarder un billet
     */
    public function save(Billet $billet): Billet;
}
