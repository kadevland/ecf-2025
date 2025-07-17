<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Seance;

use App\Domain\ValueObjects\ValueObject;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class SeanceHoraire extends ValueObject
{
    private function __construct(
        public CreneauHoraire $creneauFilm,
        public int $tempsInterSeanceMinutes
    ) {
        $this->enforceInvariants();
    }

    public static function create(
        CreneauHoraire $creneauFilm,
        int $tempsInterSeanceMinutes = 20
    ): self {
        return new self($creneauFilm, $tempsInterSeanceMinutes);
    }

    public static function fromDebutEtDuree(
        CarbonImmutable $debutSeance,
        int $dureeFilmMinutes,
        int $tempsInterSeanceMinutes = 20
    ): self {
        if ($dureeFilmMinutes <= 0) {
            throw new InvalidArgumentException('La durée du film doit être positive');
        }

        $creneauFilm = CreneauHoraire::fromDebutEtDuree($debutSeance, $dureeFilmMinutes);

        return new self($creneauFilm, $tempsInterSeanceMinutes);
    }

    public function dureeFilmMinutes(): int
    {
        return $this->creneauFilm->dureeMinutes();
    }

    public function dureeTotaleMinutes(): int
    {
        return $this->creneauFilm->dureeMinutes() + $this->tempsInterSeanceMinutes;
    }

    public function debut(): CarbonImmutable
    {
        return $this->creneauFilm->debut;
    }

    public function fin(): CarbonImmutable
    {
        return $this->creneauFilm->fin->addMinutes($this->tempsInterSeanceMinutes);
    }

    public function changerDate(CarbonImmutable $nouvelleDate): self
    {
        $nouveauDebut = $this->debut()
            ->setYear($nouvelleDate->year)
            ->setMonth($nouvelleDate->month)
            ->setDay($nouvelleDate->day);

        $nouveauCreneauFilm = CreneauHoraire::fromDebutEtDuree(
            $nouveauDebut,
            $this->creneauFilm->dureeMinutes()
        );

        return new self($nouveauCreneauFilm, $this->tempsInterSeanceMinutes);
    }

    public function changerHeure(int $heure, int $minute): self
    {
        if ($heure < 0 || $heure > 23) {
            throw new InvalidArgumentException('L\'heure doit être entre 0 et 23');
        }

        if ($minute < 0 || $minute > 59) {
            throw new InvalidArgumentException('Les minutes doivent être entre 0 et 59');
        }

        $nouveauDebut = $this->debut()->setTime($heure, $minute);

        $nouveauCreneauFilm = CreneauHoraire::fromDebutEtDuree(
            $nouveauDebut,
            $this->creneauFilm->dureeMinutes()
        );

        return new self($nouveauCreneauFilm, $this->tempsInterSeanceMinutes);
    }

    public function changerTempsPreparation(int $nouveauTempsMinutes): self
    {
        return new self($this->creneauFilm, $nouveauTempsMinutes);
    }

    public function decaler(int $minutes): self
    {
        $nouveauDebut = $this->debut()->addMinutes($minutes);

        $nouveauCreneauFilm = CreneauHoraire::fromDebutEtDuree(
            $nouveauDebut,
            $this->creneauFilm->dureeMinutes()
        );

        return new self($nouveauCreneauFilm, $this->tempsInterSeanceMinutes);
    }

    public function estEnConflit(self $autre): bool
    {
        $monCreneau   = CreneauHoraire::fromDebutEtFin($this->debut(), $this->fin());
        $autreCreneau = CreneauHoraire::fromDebutEtFin($autre->debut(), $autre->fin());

        return $monCreneau->chevauche($autreCreneau);
    }

    public function equals(self $autre): bool
    {
        return $this->creneauFilm->equals($autre->creneauFilm)
            && $this->tempsInterSeanceMinutes === $autre->tempsInterSeanceMinutes;
    }

    protected function enforceInvariants(): void
    {
        if ($this->tempsInterSeanceMinutes < 0) {
            throw new InvalidArgumentException('Le temps inter-séance ne peut pas être négatif');
        }

        if ($this->tempsInterSeanceMinutes > 120) {
            throw new InvalidArgumentException('Le temps inter-séance ne peut pas dépasser 2 heures');
        }
    }
}
