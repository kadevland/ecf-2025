<?php

declare(strict_types=1);

namespace App\Domain\Entities\Reservation;

use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\StatutReservation;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Domain\ValueObjects\User\UserId;
use BadMethodCallException;
use Carbon\CarbonImmutable;
use DomainException;
use InvalidArgumentException;

/**
 * Entité Réservation - Représente une réservation de billets
 */
final class Reservation implements EntityInterface
{
    public function __construct(
        public private(set) ReservationId $id,
        public private(set) UserId $userId,
        public private(set) SeanceId $seanceId,
        public private(set) StatutReservation $statut,
        public private(set) int $nombrePlaces,
        public private(set) Prix $prixTotal,
        public private(set) string $codeCinema,
        public private(set) ?string $numeroReservation = null,
        public private(set) ?CarbonImmutable $createdAt = null,
        public private(set) ?CarbonImmutable $updatedAt = null,
        public private(set) ?CarbonImmutable $confirmedAt = null,
        public private(set) ?CarbonImmutable $expiresAt = null,
        public private(set) ?string $notes = null,
    ) {
        $now = CarbonImmutable::now();

        // Initialiser les valeurs par défaut si null
        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }
        if ($this->updatedAt === null) {
            $this->updatedAt = $now;
        }
        if ($this->numeroReservation === null) {
            $this->numeroReservation = self::genererNumeroReservation($this->codeCinema, $this->seanceId, $this->id, $this->createdAt);
        }
        if ($this->expiresAt === null && $this->statut === StatutReservation::EnAttente) {
            $this->expiresAt = $this->createdAt->addMinutes(15);
        }

        $this->enforceInvariants();
    }

    // === MAGIC METHODS ===

    public function __get(string $name): mixed
    {
        return match ($name) {
            'estActive'  => $this->estActive(),
            'estExpiree' => $this->estExpiree(),
            default      => throw new BadMethodCallException("Property {$name} does not exist"),
        };
    }

    // === FACTORY METHODS ===

    public static function creer(
        ReservationId $id,
        UserId $userId,
        SeanceId $seanceId,
        int $nombrePlaces,
        Prix $prixTotal,
        string $codeCinema,
        ?CarbonImmutable $expiresAt = null
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            seanceId: $seanceId,
            statut: StatutReservation::EnAttente,
            nombrePlaces: $nombrePlaces,
            prixTotal: $prixTotal,
            codeCinema: $codeCinema,
            expiresAt: $expiresAt
        );
    }

    // === BUSINESS METHODS ===

    public function confirmer(): void
    {
        if ($this->statut !== StatutReservation::EnAttente) {
            throw new DomainException('Seules les réservations en attente peuvent être confirmées');
        }

        if ($this->estExpiree()) {
            throw new DomainException('Cette réservation a expiré');
        }

        $this->statut      = StatutReservation::Confirmee;
        $this->confirmedAt = CarbonImmutable::now();
        $this->touch();
    }

    public function payer(): void
    {
        if (! in_array($this->statut, [StatutReservation::EnAttente, StatutReservation::Confirmee], true)) {
            throw new DomainException('Cette réservation ne peut pas être payée');
        }

        $this->statut      = StatutReservation::Payee;
        $this->confirmedAt = $this->confirmedAt ?? CarbonImmutable::now();
        $this->touch();
    }

    public function annuler(?string $raison = null): void
    {
        if (! $this->peutEtreAnnulee()) {
            throw new DomainException('Cette réservation ne peut pas être annulée');
        }

        $this->statut = StatutReservation::Annulee;
        $this->notes  = $raison;
        $this->touch();
    }

    public function terminer(): void
    {
        if ($this->statut !== StatutReservation::Payee) {
            throw new DomainException('Seules les réservations payées peuvent être terminées');
        }

        $this->statut = StatutReservation::Terminee;
        $this->touch();
    }

    public function expirer(): void
    {
        if ($this->statut !== StatutReservation::EnAttente) {
            return;
        }

        $this->statut = StatutReservation::Expiree;
        $this->touch();
    }

    // === QUERY METHODS ===

    public function estExpiree(): bool
    {
        return $this->expiresAt !== null &&
            $this->expiresAt->isPast() &&
            $this->statut === StatutReservation::EnAttente;
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [
            StatutReservation::EnAttente,
            StatutReservation::Confirmee,
            StatutReservation::Payee,
        ], true);
    }

    public function estActive(): bool
    {
        return $this->statut->estActive();
    }

    // === EQUALITY ===

    public function equals(EntityInterface $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    /**
     * Génère un numéro de réservation unique
     * Format: [CODE_CINEMA][YY][M][4xUUID_Seance][4xUUID_Resa]
     */
    private static function genererNumeroReservation(
        string $codeCinema,
        SeanceId $seanceId,
        ReservationId $reservationId,
        CarbonImmutable $createdAt
    ): string {
        // Année sur 2 chiffres
        $annee = $createdAt->format('y');

        // Mois en 1-9OND
        $moisMapping = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'O', 'N', 'D'];
        $mois        = $moisMapping[(int) $createdAt->format('n') - 1];

        // 4 premiers chars UUID seance (sans tirets)
        $seanceShort = mb_substr(str_replace('-', '', $seanceId->uuid), 0, 4);

        // 4 premiers chars UUID reservation (sans tirets)
        $resaShort = mb_substr(str_replace('-', '', $reservationId->uuid), 0, 4);

        return "{$codeCinema}{$annee}{$mois}{$seanceShort}{$resaShort}";
    }

    // === PRIVATE METHODS ===

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }

    private function enforceInvariants(): void
    {
        if ($this->nombrePlaces <= 0) {
            throw new InvalidArgumentException('Le nombre de places doit être positif');
        }

        if (empty($this->codeCinema)) {
            throw new InvalidArgumentException('Le code cinéma est obligatoire');
        }

        $lengthCinema = mb_strlen($this->codeCinema);
        if ($lengthCinema !== 3 && $lengthCinema !== 4) {
            throw new InvalidArgumentException('Le code cinéma doit faire 3 ou 4 caractères');
        }

        if ($this->createdAt === null) {
            throw new InvalidArgumentException('La date de création ne peut pas être null');
        }

        if ($this->updatedAt === null) {
            throw new InvalidArgumentException('La date de mise à jour ne peut pas être null');
        }

        if (empty($this->numeroReservation)) {
            throw new InvalidArgumentException('Le numéro de réservation est obligatoire');
        }

        // Vérifier que le numéro de réservation correspond au format attendu (13 ou 14 caractères)
        $length = mb_strlen($this->numeroReservation);
        if ($length !== 13 && $length !== 14) {
            throw new InvalidArgumentException('Le numéro de réservation doit faire 13 ou 14 caractères');
        }

        // Vérifier que le numéro commence par le bon code cinéma
        if (!str_starts_with($this->numeroReservation, $this->codeCinema)) {
            throw new InvalidArgumentException('Le numéro de réservation doit commencer par le code cinéma');
        }
    }
}
