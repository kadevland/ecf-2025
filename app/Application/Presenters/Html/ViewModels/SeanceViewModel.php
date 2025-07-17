<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Seance\Seance;
use App\Domain\Enums\EtatSeance;
use App\Domain\ValueObjects\Commun\Prix;
use DateTimeImmutable;

final readonly class SeanceViewModel
{
    public function __construct(
        private Seance $seance,
        private ?FilmViewModel $filmViewModel = null,
        private ?SalleViewModel $salleViewModel = null,
        private ?CinemaViewModel $cinemaViewModel = null
    ) {}

    /**
     * Informations de base
     */
    public function id(): string
    {
        return $this->seance->id()->value();
    }

    public function dateHeure(): DateTimeImmutable
    {
        return $this->seance->seanceHoraire->debut();
    }

    public function date(): string
    {
        return $this->dateHeure()->format('d/m/Y');
    }

    public function heure(): string
    {
        return $this->dateHeure()->format('H:i');
    }

    public function jourSemaine(): string
    {
        $jours = [
            'Monday'    => 'Lundi',
            'Tuesday'   => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday'  => 'Jeudi',
            'Friday'    => 'Vendredi',
            'Saturday'  => 'Samedi',
            'Sunday'    => 'Dimanche',
        ];

        $jour = $this->dateHeure()->format('l');

        return $jours[$jour] ?? $jour;
    }

    public function dateComplete(): string
    {
        return sprintf(
            '%s %s à %s',
            $this->jourSemaine(),
            $this->date(),
            $this->heure()
        );
    }

    /**
     * Version et langue
     */
    public function version(): string
    {
        // TODO: Version should be stored in the domain entity or retrieved from film
        return 'VF'; // Default version for now
    }

    public function badgeVersion(): string
    {
        $version = $this->version();

        return match ($version) {
            'VF'     => 'Version Française',
            'VOSTFR' => 'VOST-FR',
            'VO'     => 'Version Originale',
            default  => $version,
        };
    }

    public function classeBadgeVersion(): string
    {
        $version = $this->version();

        return match ($version) {
            'VF'     => 'badge-primary',
            'VOSTFR' => 'badge-secondary',
            'VO'     => 'badge-accent',
            default  => 'badge-neutral',
        };
    }

    public function qualiteProjection(): string
    {
        return $this->seance->qualiteProjection->value;
    }

    /**
     * État et disponibilité
     */
    public function etat(): string
    {
        return $this->seance->etat->label();
    }

    public function estProgrammee(): bool
    {
        return $this->seance->etat === EtatSeance::Programmee;
    }

    public function estAnnulee(): bool
    {
        return $this->seance->etat === EtatSeance::Annulee;
    }

    public function estPassee(): bool
    {
        return $this->dateHeure() < new DateTimeImmutable();
    }

    public function estAujourdhui(): bool
    {
        return $this->dateHeure()->format('Y-m-d') === date('Y-m-d');
    }

    public function estDansLeFutur(): bool
    {
        return $this->dateHeure() > new DateTimeImmutable();
    }

    public function minutesAvantDebut(): int
    {
        if ($this->estPassee()) {
            return -1;
        }

        $maintenant = new DateTimeImmutable();
        $diff       = $this->dateHeure()->getTimestamp() - $maintenant->getTimestamp();

        return (int) ($diff / 60);
    }

    public function tempsPourReserver(): string
    {
        $minutes = $this->minutesAvantDebut();

        if ($minutes < 0) {
            return 'Séance passée';
        }

        if ($minutes < 30) {
            return 'Réservation fermée';
        }

        if ($minutes < 60) {
            return sprintf('%d min', $minutes);
        }

        $heures = (int) ($minutes / 60);
        if ($heures < 24) {
            return sprintf('%dh', $heures);
        }

        $jours = (int) ($heures / 24);

        return sprintf('%d jour%s', $jours, $jours > 1 ? 's' : '');
    }

    public function peutEtreReservee(): bool
    {
        return $this->estProgrammee()
            && ! $this->estPassee()
            && $this->minutesAvantDebut() >= 30
            && $this->placesDisponibles() > 0;
    }

    /**
     * Places et disponibilité
     */
    public function capaciteTotale(): int
    {
        return $this->seance->nombrePlacesTotalGlobal();
    }

    public function placesVendues(): int
    {
        return $this->seance->nombrePlacesOccupeesGlobal();
    }

    public function placesDisponibles(): int
    {
        return $this->seance->nombrePlacesDisponiblesGlobal();
    }

    public function tauxRemplissage(): float
    {
        $capacite = $this->capaciteTotale();
        if ($capacite === 0) {
            return 0;
        }

        return round(($this->placesVendues() / $capacite) * 100, 1);
    }

    public function badgeDisponibilite(): string
    {
        $disponibles = $this->placesDisponibles();
        $taux        = $this->tauxRemplissage();

        if ($disponibles === 0) {
            return 'Complet';
        }

        if ($taux >= 90) {
            return 'Dernières places';
        }

        if ($taux >= 70) {
            return sprintf('%d places', $disponibles);
        }

        return 'Disponible';
    }

    public function classeBadgeDisponibilite(): string
    {
        $taux = $this->tauxRemplissage();

        return match (true) {
            $taux >= 100 => 'badge-error',
            $taux >= 90  => 'badge-warning',
            $taux >= 70  => 'badge-info',
            default      => 'badge-success',
        };
    }

    /**
     * Tarification
     */
    public function tarif(): string
    {
        return $this->formatPrix($this->seance->prixBase);
    }

    public function tarifEtudiant(): string
    {
        // TODO: Implement student pricing rules in domain
        return $this->formatPrix($this->seance->prixBase);
    }

    public function tarifSenior(): string
    {
        // TODO: Implement senior pricing rules in domain
        return $this->formatPrix($this->seance->prixBase);
    }

    public function tarifEnfant(): string
    {
        // TODO: Implement child pricing rules in domain
        return $this->formatPrix($this->seance->prixBase);
    }

    public function tarifsSpeciaux(): array
    {
        return [
            ['label' => 'Tarif normal', 'prix' => $this->tarif()],
            ['label' => 'Étudiant', 'prix' => $this->tarifEtudiant()],
            ['label' => 'Senior (+65 ans)', 'prix' => $this->tarifSenior()],
            ['label' => 'Enfant (-12 ans)', 'prix' => $this->tarifEnfant()],
        ];
    }

    /**
     * Relations (utilise les ViewModels injectés)
     */
    public function film(): ?FilmViewModel
    {
        return $this->filmViewModel;
    }

    public function salle(): ?SalleViewModel
    {
        return $this->salleViewModel;
    }

    public function cinema(): ?CinemaViewModel
    {
        return $this->cinemaViewModel;
    }

    public function titreFilm(): string
    {
        return $this->filmViewModel?->titre() ?? 'Film non défini';
    }

    public function nomSalle(): string
    {
        return $this->salleViewModel?->nomComplet() ?? 'Salle non définie';
    }

    public function nomCinema(): string
    {
        return $this->cinemaViewModel?->nom() ?? 'Cinéma non défini';
    }

    /**
     * Badges et indicateurs spéciaux
     */
    public function badges(): array
    {
        $badges = [];

        // Badge version
        $badges[] = [
            'label' => $this->badgeVersion(),
            'class' => $this->classeBadgeVersion(),
        ];

        // Badge qualité salle
        if ($this->salleViewModel && $this->salleViewModel->estPremium()) {
            $badges[] = [
                'label' => $this->salleViewModel->qualiteProjection(),
                'class' => 'badge-accent',
            ];
        }

        // Badge nouveauté
        if ($this->filmViewModel && $this->filmViewModel->estSortiRecemment()) {
            $badges[] = [
                'label' => 'Nouveauté',
                'class' => 'badge-secondary',
            ];
        }

        // Badge disponibilité
        if ($this->tauxRemplissage() >= 90 && $this->placesDisponibles() > 0) {
            $badges[] = [
                'label' => 'Dernières places',
                'class' => 'badge-warning',
            ];
        }

        return $badges;
    }

    /**
     * Horaires spéciaux
     */
    public function estSeanceMatinale(): bool
    {
        $heure = (int) ($this->dateHeure()->format('H'));

        return $heure < 12;
    }

    public function estSeanceSoiree(): bool
    {
        $heure = (int) ($this->dateHeure()->format('H'));

        return $heure >= 18;
    }

    public function estSeanceNocturne(): bool
    {
        $heure = (int) ($this->dateHeure()->format('H'));

        return $heure >= 22 || $heure < 6;
    }

    public function periodeJournee(): string
    {
        return match (true) {
            $this->estSeanceNocturne() => 'Nocturne',
            $this->estSeanceMatinale() => 'Matinée',
            $this->estSeanceSoiree()   => 'Soirée',
            default                    => 'Après-midi',
        };
    }

    /**
     * Actions et liens
     */
    public function lienReserver(): string
    {
        return route('reservations.create', ['seance' => $this->id()]);
    }

    public function lienDetail(): string
    {
        return route('seances.show', $this->id());
    }

    public function lienAdminDetail(): string
    {
        return '#'; // route('gestion.supervision.seances.show', $this->id());
    }

    public function lienAdminModifier(): string
    {
        return '#'; // route('gestion.supervision.seances.edit', $this->id());
    }

    public function lienAdminSupprimer(): string
    {
        return '#'; // route('gestion.supervision.seances.destroy', $this->id());
    }

    /**
     * Permissions
     */
    public function peutEtreModifiee(): bool
    {
        return ! $this->estPassee() && ! $this->estAnnulee();
    }

    public function peutEtreSupprimee(): bool
    {
        // Ne peut être supprimée que si pas de réservations et dans le futur
        return $this->placesVendues() === 0 && $this->estDansLeFutur();
    }

    public function peutEtreAnnulee(): bool
    {
        return $this->estProgrammee() && $this->estDansLeFutur();
    }

    /**
     * Messages et descriptions
     */
    public function descriptionComplete(): string
    {
        return sprintf(
            '%s - %s - %s (%s) - %s',
            $this->titreFilm(),
            $this->dateComplete(),
            $this->nomSalle(),
            $this->badgeVersion(),
            $this->tarif()
        );
    }

    public function messageIndisponibilite(): ?string
    {
        if ($this->estAnnulee()) {
            return 'Cette séance a été annulée';
        }

        if ($this->estPassee()) {
            return 'Cette séance est terminée';
        }

        if ($this->placesDisponibles() === 0) {
            return 'Cette séance est complète';
        }

        if ($this->minutesAvantDebut() < 30) {
            return 'Les réservations sont fermées (moins de 30 minutes avant la séance)';
        }

        return null;
    }

    private function formatPrix(Prix $prix): string
    {
        return number_format($prix->getAmount() / 100, 2, ',', ' ').' €';
    }
}
