<?php

declare(strict_types=1);

namespace App\Domain\Entities\Billet;

use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\TypeTarif;
use App\Domain\ValueObjects\Billet\BilletId;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

/**
 * Entité Billet
 */
final class Billet implements EntityInterface
{
    public function __construct(
        public private(set) ?BilletId $id,
        public private(set) ReservationId $reservationId,
        public private(set) SeanceId $seanceId,
        public private(set) string $numeroBillet,
        public private(set) string $place,
        public private(set) TypeTarif $typeTarif,
        public private(set) Prix $prix,
        public private(set) ?string $qrCode,
        public private(set) bool $utilise,
        public private(set) ?CarbonImmutable $dateUtilisation,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();
    }

    /**
     * Créer un nouveau billet
     */
    public static function create(
        ReservationId $reservationId,
        SeanceId $seanceId,
        string $numeroBillet,
        string $place,
        TypeTarif $typeTarif,
        Prix $prix
    ): self {
        return new self(
            id: BilletId::generate(),
            reservationId: $reservationId,
            seanceId: $seanceId,
            numeroBillet: $numeroBillet,
            place: $place,
            typeTarif: $typeTarif,
            prix: $prix,
            qrCode: null,
            utilise: false,
            dateUtilisation: null,
            createdAt: CarbonImmutable::now(),
            updatedAt: CarbonImmutable::now(),
        );
    }

    /**
     * Marquer le billet comme utilisé
     */
    public function marquerUtilise(): void
    {
        if ($this->utilise) {
            throw new InvalidArgumentException('Le billet est déjà utilisé');
        }

        $this->utilise         = true;
        $this->dateUtilisation = CarbonImmutable::now();
        $this->touch();
    }

    /**
     * Ajouter un QR code
     */
    public function ajouterQrCode(string $qrCode): void
    {
        $this->qrCode = $qrCode;
        $this->touch();
    }

    /**
     * Vérifier si le billet peut être utilisé
     */
    public function peutEtreUtilise(): bool
    {
        return ! $this->utilise;
    }

    /**
     * Comparer avec une autre entité
     */
    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->id?->equals($other->id) === true;
    }

    /**
     * Vérifier les invariants métier
     */
    private function enforceInvariants(): void
    {
        if (empty(mb_trim($this->numeroBillet))) {
            throw new InvalidArgumentException('Le numéro de billet ne peut pas être vide');
        }

        if (empty(mb_trim($this->place))) {
            throw new InvalidArgumentException('La place ne peut pas être vide');
        }

        if ($this->utilise && $this->dateUtilisation === null) {
            throw new InvalidArgumentException('Un billet utilisé doit avoir une date d\'utilisation');
        }

        if (! $this->utilise && $this->dateUtilisation !== null) {
            throw new InvalidArgumentException('Un billet non utilisé ne doit pas avoir de date d\'utilisation');
        }
    }

    /**
     * Mettre à jour la date de modification
     */
    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
