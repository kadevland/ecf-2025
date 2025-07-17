<?php

declare(strict_types=1);

namespace App\Domain\Entities\Seance;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Film\Film;
use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use App\Domain\Events\Seance\SeanceCreatedEvent;
use App\Domain\Traits\RecordsDomainEvents;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Domain\ValueObjects\Seance\SeanceHoraire;
use App\Domain\ValueObjects\Seance\SeanceId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class Seance implements EntityInterface
{
    use RecordsDomainEvents;

    public function __construct(
        public private(set) SeanceId $id,
        public private(set) FilmId $filmId,
        public private(set) CinemaId $cinemaId,
        public private(set) SalleId $salleId,
        public private(set) SeanceHoraire $seanceHoraire,
        public private(set) QualiteProjection $qualiteProjection,
        public private(set) Prix $prixBase,
        public private(set) int $nombrePlacesTotal,
        public private(set) int $nombrePlacesDisponibles,
        public private(set) int $nombrePlacesPmrTotal,
        public private(set) int $nombrePlacesPmrDisponibles,
        public private(set) EtatSeance $etat,
        public private(set) ?string $notes,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();
        $this->recordEvent(new SeanceCreatedEvent($this->id, $this->filmId, $this->seanceHoraire->debut()));
    }

    public static function creer(
        FilmId $filmId,
        CinemaId $cinemaId,
        SalleId $salleId,
        SeanceHoraire $seanceHoraire,
        QualiteProjection $qualiteProjection,
        Prix $prixBase,
        int $nombrePlacesTotal,
        int $nombrePlacesPmrTotal,
        ?string $notes = null
    ): self {
        $now = CarbonImmutable::now();

        return new self(
            SeanceId::generate(),
            $filmId,
            $cinemaId,
            $salleId,
            $seanceHoraire,
            $qualiteProjection,
            $prixBase,
            $nombrePlacesTotal,
            $nombrePlacesTotal,
            $nombrePlacesPmrTotal,
            $nombrePlacesPmrTotal,
            EtatSeance::EnCoursProgrammation,
            $notes,
            $now,
            $now
        );
    }

    public function reprogrammer(SeanceHoraire $nouveauSeanceHoraire): void
    {
        $this->verifierModificationAutorisee();
        $this->validerSeanceHoraire($nouveauSeanceHoraire);

        if ($this->seanceHoraire->equals($nouveauSeanceHoraire)) {
            return;
        }

        $this->seanceHoraire = $nouveauSeanceHoraire;
        $this->touch();
    }

    public function changerDate(CarbonImmutable $nouvelleDate): void
    {
        $this->verifierModificationAutorisee();
        $nouveauSeanceHoraire = $this->seanceHoraire->changerDate($nouvelleDate);
        $this->reprogrammer($nouveauSeanceHoraire);
    }

    public function changerHeure(int $heure, int $minute): void
    {
        $this->verifierModificationAutorisee();
        $nouveauSeanceHoraire = $this->seanceHoraire->changerHeure($heure, $minute);
        $this->reprogrammer($nouveauSeanceHoraire);
    }

    public function changerTempsPreparation(int $nouveauTempsMinutes): void
    {
        $this->verifierModificationAutorisee();
        $nouveauSeanceHoraire = $this->seanceHoraire->changerTempsPreparation($nouveauTempsMinutes);
        $this->reprogrammer($nouveauSeanceHoraire);
    }

    public function decaler(int $minutes): void
    {
        $this->verifierModificationAutorisee();
        $nouveauSeanceHoraire = $this->seanceHoraire->decaler($minutes);
        $this->reprogrammer($nouveauSeanceHoraire);
    }

    public function changerFilm(Film $nouveauFilm, ?QualiteProjection $qualiteProjection = null): void
    {
        $this->verifierModificationAutorisee();
        $nouvelleQualite = $qualiteProjection ?? $this->qualiteProjection;

        // Vérifier que le nouveau film supporte la qualité de projection demandée
        if (! in_array($nouvelleQualite, $nouveauFilm->qualitesDisponibles, true)) {
            throw new InvalidArgumentException(
                "Le film '{$nouveauFilm->titre}' ne supporte pas la qualité {$nouvelleQualite->value}"
            );
        }

        // Éviter les changements inutiles
        if ($this->filmId->equals($nouveauFilm->id) && $this->qualiteProjection === $nouvelleQualite) {
            return;
        }

        // Recréer le SeanceHoraire avec la nouvelle durée du film
        $nouveauSeanceHoraire = SeanceHoraire::fromDebutEtDuree(
            $this->seanceHoraire->debut(),
            $nouveauFilm->dureeMinutes,
            $this->seanceHoraire->tempsInterSeanceMinutes
        );

        $this->filmId            = $nouveauFilm->id;
        $this->qualiteProjection = $nouvelleQualite;
        $this->seanceHoraire     = $nouveauSeanceHoraire;
        $this->touch();
    }

    public function changerPrixBase(Prix $nouveauPrix): void
    {
        $this->verifierModificationAutorisee();

        if ($this->prixBase->equals($nouveauPrix)) {
            return;
        }

        $this->prixBase = $nouveauPrix;
        $this->touch();
    }

    public function changerNotes(?string $nouvellesNotes): void
    {
        $this->verifierModificationAutorisee();

        if ($nouvellesNotes !== null) {
            $this->validerNotes($nouvellesNotes);
        }

        if ($this->notes === $nouvellesNotes) {
            return;
        }

        $this->notes = $nouvellesNotes;
        $this->touch();
    }

    public function reserverPlaces(int $nombrePlaces): void
    {
        if ($nombrePlaces <= 0) {
            return;
        }

        $this->verifierReservationAutorisee();

        if ($nombrePlaces > $this->nombrePlacesDisponibles) {
            throw new InvalidArgumentException('Pas assez de places disponibles');
        }

        $this->nombrePlacesDisponibles -= $nombrePlaces;
        $this->touch();
    }

    public function reserverPlacesPmr(int $nombrePlacesPmr): void
    {
        if ($nombrePlacesPmr <= 0) {
            return;
        }

        $this->verifierReservationAutorisee();

        if ($nombrePlacesPmr > $this->nombrePlacesPmrDisponibles) {
            throw new InvalidArgumentException('Pas assez de places PMR disponibles');
        }

        $this->nombrePlacesPmrDisponibles -= $nombrePlacesPmr;
        $this->touch();
    }

    public function libererPlaces(int $nombrePlaces): void
    {
        if ($nombrePlaces <= 0) {
            return;
        }

        $this->verifierLiberationAutorisee();

        $nouvelleDisponibilite = $this->nombrePlacesDisponibles + $nombrePlaces;

        if ($nouvelleDisponibilite > $this->nombrePlacesTotal) {
            throw new InvalidArgumentException('Impossible de libérer plus de places que le total');
        }

        $this->nombrePlacesDisponibles = $nouvelleDisponibilite;
        $this->touch();
    }

    public function libererPlacesPmr(int $nombrePlacesPmr): void
    {
        if ($nombrePlacesPmr <= 0) {
            return;
        }

        $this->verifierLiberationAutorisee();

        $nouvelleDisponibilitePmr = $this->nombrePlacesPmrDisponibles + $nombrePlacesPmr;

        if ($nouvelleDisponibilitePmr > $this->nombrePlacesPmrTotal) {
            throw new InvalidArgumentException('Impossible de libérer plus de places PMR que le total');
        }

        $this->nombrePlacesPmrDisponibles = $nouvelleDisponibilitePmr;
        $this->touch();
    }

    public function programmer(): void
    {
        $this->changerEtat(EtatSeance::Programmee);
    }

    public function annuler(): void
    {
        $this->changerEtat(EtatSeance::Annulee);
    }

    public function changerEtat(EtatSeance $nouvelEtat): void
    {
        if ($this->etat === $nouvelEtat) {
            return;
        }

        if (! $this->etat->peutPasserA($nouvelEtat)) {
            throw new InvalidArgumentException(
                "Impossible de passer de l'état {$this->etat->label()} à {$nouvelEtat->label()}"
            );
        }

        $this->etat = $nouvelEtat;
        $this->touch();
    }

    public function estPassee(?CarbonImmutable $reference = null): bool
    {
        $reference ??= CarbonImmutable::now();

        return $this->seanceHoraire->fin()->lt($reference);
    }

    public function estAVenir(?CarbonImmutable $reference = null): bool
    {
        return ! $this->estPassee($reference);
    }

    public function estCompleteReservee(): bool
    {
        return $this->nombrePlacesDisponibles === 0;
    }

    public function estCompletementReserveePmr(): bool
    {
        return $this->nombrePlacesPmrDisponibles === 0;
    }

    public function tauxOccupation(): float
    {
        if ($this->nombrePlacesTotal === 0) {
            return 0.0;
        }

        $placesReservees = $this->nombrePlacesTotal - $this->nombrePlacesDisponibles;

        return round(($placesReservees / $this->nombrePlacesTotal) * 100, 2);
    }

    public function tauxOccupationPmr(): float
    {
        if ($this->nombrePlacesPmrTotal === 0) {
            return 0.0;
        }

        $placesReserveesPmr = $this->nombrePlacesPmrTotal - $this->nombrePlacesPmrDisponibles;

        return round(($placesReserveesPmr / $this->nombrePlacesPmrTotal) * 100, 2);
    }

    public function nombrePlacesTotalGlobal(): int
    {
        return $this->nombrePlacesTotal + $this->nombrePlacesPmrTotal;
    }

    public function nombrePlacesDisponiblesGlobal(): int
    {
        return $this->nombrePlacesDisponibles + $this->nombrePlacesPmrDisponibles;
    }

    public function nombrePlacesOccupeesGlobal(): int
    {
        return $this->nombrePlacesTotalGlobal() - $this->nombrePlacesDisponiblesGlobal();
    }

    public function tauxOccupationGlobal(): float
    {
        $total = $this->nombrePlacesTotalGlobal();

        if ($total === 0) {
            return 0.0;
        }

        return round(($this->nombrePlacesOccupeesGlobal() / $total) * 100, 2);
    }

    public function peutEtreReservee(): bool
    {
        return $this->peutAccepterReservations()
            && $this->estAVenir()
            && $this->nombrePlacesDisponibles > 0;
    }

    public function peutEtreModifiee(): bool
    {
        return $this->etat === EtatSeance::EnCoursProgrammation;
    }

    public function peutAccepterReservations(): bool
    {
        return $this->etat === EtatSeance::Programmee;
    }

    public function reservationsSontFigees(): bool
    {
        return $this->etat === EtatSeance::Annulee;
    }

    public function estEnVente(): bool
    {
        return $this->etat === EtatSeance::Programmee;
    }

    public function estAnnulee(): bool
    {
        return $this->etat === EtatSeance::Annulee;
    }

    public function estEnCoursProgrammation(): bool
    {
        return $this->etat === EtatSeance::EnCoursProgrammation;
    }

    public function estProgrammee(): bool
    {
        return $this->etat === EtatSeance::Programmee;
    }

    public function estEnConflitAvec(self $autre): bool
    {
        // Même salle et horaires qui se chevauchent
        return $this->salleId->equals($autre->salleId)
            && $this->seanceHoraire->estEnConflit($autre->seanceHoraire);
    }

    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->id->equals($other->id);
    }

    private function enforceInvariants(): void
    {
        $this->validerSeanceHoraire($this->seanceHoraire);
        $this->validerNombrePlaces($this->nombrePlacesTotal, $this->nombrePlacesDisponibles);
        $this->validerNombrePlacesPmr($this->nombrePlacesPmrTotal, $this->nombrePlacesPmrDisponibles);

        if ($this->notes !== null) {
            $this->validerNotes($this->notes);
        }

    }

    private function validerSeanceHoraire(SeanceHoraire $seanceHoraire): void
    {
        // Validation désactivée pour permettre l'affichage des séances passées dans l'admin
        // if ($seanceHoraire->debut()->lt(CarbonImmutable::now())) {
        //     throw new InvalidArgumentException('La séance ne peut pas être dans le passé');
        // }
    }

    private function validerNombrePlaces(int $total, int $disponibles): void
    {
        if ($total <= 0) {
            throw new InvalidArgumentException('Le nombre total de places doit être positif');
        }

        if ($disponibles < 0) {
            throw new InvalidArgumentException('Le nombre de places disponibles ne peut pas être négatif');
        }

        if ($disponibles > $total) {
            throw new InvalidArgumentException('Le nombre de places disponibles ne peut pas dépasser le total');
        }
    }

    private function validerNombrePlacesPmr(int $total, int $disponibles): void
    {
        if ($total < 0) {
            throw new InvalidArgumentException('Le nombre total de places PMR ne peut pas être négatif');
        }

        if ($disponibles < 0) {
            throw new InvalidArgumentException('Le nombre de places PMR disponibles ne peut pas être négatif');
        }

        if ($disponibles > $total) {
            throw new InvalidArgumentException('Le nombre de places PMR disponibles ne peut pas dépasser le total');
        }
    }

    private function validerNotes(string $notes): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 1000)
                ->assert($notes);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Notes invalides: doivent contenir entre 1 et 1000 caractères');
        }
    }

    private function verifierModificationAutorisee(): void
    {
        if (! $this->peutEtreModifiee()) {
            throw new InvalidArgumentException(
                "Impossible de modifier une séance en état {$this->etat->label()}"
            );
        }
    }

    private function verifierReservationAutorisee(): void
    {
        if (! $this->peutAccepterReservations()) {
            throw new InvalidArgumentException(
                "Les réservations ne sont pas autorisées pour une séance en état {$this->etat->label()}"
            );
        }
    }

    private function verifierLiberationAutorisee(): void
    {
        if ($this->reservationsSontFigees()) {
            throw new InvalidArgumentException(
                "Impossible de libérer des places pour une séance en état {$this->etat->label()}"
            );
        }
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
