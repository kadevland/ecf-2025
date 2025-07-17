<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Seance;

use App\Domain\ValueObjects\ValueObject;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class CreneauHoraire extends ValueObject
{
    private function __construct(
        public CarbonImmutable $debut,
        public CarbonImmutable $fin
    ) {
        $this->enforceInvariants();
    }

    public static function fromDebutEtFin(
        CarbonImmutable $debut,
        CarbonImmutable $fin
    ): self {
        return new self($debut, $fin);
    }

    public static function fromDebutEtDuree(
        CarbonImmutable $debut,
        int $dureeMinutes
    ): self {
        if ($dureeMinutes <= 0) {
            throw new InvalidArgumentException('La durée doit être positive');
        }

        return new self($debut, $debut->addMinutes($dureeMinutes));
    }

    public function dureeMinutes(): int
    {
        return (int) $this->debut->diffInMinutes($this->fin);
    }

    public function dureeHeures(): float
    {
        return round($this->dureeMinutes() / 60, 2);
    }

    public function contient(CarbonImmutable $moment): bool
    {
        return $moment->between($this->debut, $this->fin);
    }

    public function chevauche(self $autre): bool
    {
        return $this->debut->lt($autre->fin) && $this->fin->gt($autre->debut);
    }

    public function estAvant(self $autre): bool
    {
        return $this->fin->lte($autre->debut);
    }

    public function estApres(self $autre): bool
    {
        return $this->debut->gte($autre->fin);
    }

    public function estPasse(?CarbonImmutable $reference = null): bool
    {
        $maintenant = $reference ?? CarbonImmutable::now();

        return $this->fin->lt($maintenant);
    }

    public function estAVenir(?CarbonImmutable $reference = null): bool
    {
        $maintenant = $reference ?? CarbonImmutable::now();

        return $this->debut->gt($maintenant);
    }

    public function estEnCours(?CarbonImmutable $reference = null): bool
    {
        $maintenant = $reference ?? CarbonImmutable::now();

        return $this->contient($maintenant);
    }

    public function tempsRestant(?CarbonImmutable $reference = null): int
    {
        $maintenant = $reference ?? CarbonImmutable::now();

        if ($this->estPasse($reference)) {
            return 0;
        }

        if ($this->estAVenir($reference)) {
            return $this->dureeMinutes();
        }

        return (int) $maintenant->diffInMinutes($this->fin);
    }

    public function equals(self $autre): bool
    {
        return $this->debut->equalTo($autre->debut)
            && $this->fin->equalTo($autre->fin);
    }

    protected function enforceInvariants(): void
    {
        if ($this->fin->lte($this->debut)) {
            throw new InvalidArgumentException('La fin du créneau doit être après le début');
        }

        if ($this->dureeMinutes() > 600) { // 10 heures max
            throw new InvalidArgumentException('Un créneau ne peut pas dépasser 10 heures');
        }
    }
}
