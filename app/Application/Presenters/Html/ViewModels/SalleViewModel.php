<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Salle\Salle;
use App\Domain\Enums\EtatSalle;
use App\Domain\Enums\QualiteProjection;

final readonly class SalleViewModel
{
    public function __construct(
        private Salle $salle
    ) {}

    /**
     * Informations de base
     */
    public function id(): string
    {
        return $this->salle->id->uuid;
    }

    public function nom(): string
    {
        return $this->salle->numero->valeur();
    }

    public function numero(): string
    {
        return $this->salle->numero->valeur();
    }

    public function nomComplet(): string
    {
        return sprintf('Salle %s', $this->numero());
    }

    /**
     * Capacité
     */
    public function capacite(): int
    {
        return $this->salle->capaciteMaximale();
    }

    public function nombrePlacesPMR(): int
    {
        return $this->salle->nombreSiegesPMR();
    }

    public function pourcentagePMR(): float
    {
        $capacite = $this->capacite();
        if ($capacite === 0) {
            return 0;
        }

        return round(($this->nombrePlacesPMR() / $capacite) * 100, 1);
    }

    /**
     * État et statut
     */
    public function etat(): string
    {
        return match ($this->salle->etat) {
            EtatSalle::Active        => 'Active',
            EtatSalle::Maintenance   => 'En maintenance',
            EtatSalle::HorsService   => 'Hors service',
            EtatSalle::EnRenovation  => 'En rénovation',
            EtatSalle::Fermee        => 'Fermée',
        };
    }

    public function estActive(): bool
    {
        return $this->salle->etat === EtatSalle::Active;
    }

    public function classeBadgeEtat(): string
    {
        return match ($this->salle->etat) {
            EtatSalle::Active        => 'badge-success',
            EtatSalle::Maintenance   => 'badge-warning',
            EtatSalle::HorsService   => 'badge-error',
            EtatSalle::EnRenovation  => 'badge-info',
            EtatSalle::Fermee        => 'badge-neutral',
        };
    }

    /**
     * Qualités de projection supportées
     */
    public function qualitesProjection(): string
    {
        $qualites = $this->salle->qualitesProjectionSupportees;

        if (empty($qualites)) {
            return 'Standard';
        }

        $labels = array_map(
            fn (QualiteProjection $qualite): string => $qualite->label(),
            $qualites
        );

        return implode(', ', $labels);
    }

    public function estPremium(): bool
    {
        $qualitesPremium = [
            QualiteProjection::IMAX,
            QualiteProjection::QuatreDX,
            QualiteProjection::DolbyAtmos,
            QualiteProjection::ScreenX,
            QualiteProjection::ICE,
        ];

        return ! empty(array_intersect($this->salle->qualitesProjectionSupportees, $qualitesPremium));
    }

    public function badgesQualite(): array
    {
        $badges   = [];
        $qualites = $this->salle->qualitesProjectionSupportees;

        // Badge pour chaque qualité de projection
        foreach ($qualites as $qualite) {
            $isPremium = in_array($qualite, [
                QualiteProjection::IMAX,
                QualiteProjection::QUATRE_DX,
                QualiteProjection::DOLBY_ATMOS,
                QualiteProjection::SCREENX,
                QualiteProjection::ICE_IMMERSIVE,
            ]);

            $badges[] = [
                'label' => $qualite->label(),
                'class' => $isPremium ? 'badge-accent' : 'badge-primary',
            ];
        }

        // Badges supplémentaires selon équipements
        $equipements = json_decode($this->salle->equipements ?? '[]', true);

        if (in_array('son_dolby', $equipements)) {
            $badges[] = ['label' => 'Dolby', 'class' => 'badge-secondary'];
        }

        if (in_array('siege_premium', $equipements)) {
            $badges[] = ['label' => 'Sièges Premium', 'class' => 'badge-secondary'];
        }

        if ($this->nombrePlacesPMR() > 0) {
            $badges[] = ['label' => 'Accès PMR', 'class' => 'badge-info'];
        }

        return $badges;
    }

    /**
     * Équipements
     */
    public function equipements(): array
    {
        return json_decode($this->salle->equipements ?? '[]', true);
    }

    public function equipementsFormates(): array
    {
        $equipements = $this->equipements();
        $mapping     = [
            'son_dolby'         => 'Son Dolby',
            'son_atmos'         => 'Dolby Atmos',
            'projection_laser'  => 'Projection Laser',
            'siege_premium'     => 'Sièges Premium',
            'siege_dbox'        => 'Sièges D-BOX',
            'climatisation'     => 'Climatisation',
            'acces_pmr'         => 'Accès PMR',
            'boucle_magnetique' => 'Boucle magnétique',
        ];

        $formattedEquipements = [];
        foreach ($equipements as $equipement) {
            $formattedEquipements[] = $mapping[$equipement] ?? $equipement;
        }

        return $formattedEquipements;
    }

    public function iconsEquipements(): array
    {
        $icons       = [];
        $equipements = $this->equipements();

        $mapping = [
            'son_dolby'         => 'volume-high',
            'son_atmos'         => 'speaker',
            'projection_laser'  => 'projector',
            'siege_premium'     => 'sofa',
            'siege_dbox'        => 'armchair',
            'climatisation'     => 'snowflake',
            'acces_pmr'         => 'wheelchair',
            'boucle_magnetique' => 'hearing',
        ];

        foreach ($equipements as $equipement) {
            if (isset($mapping[$equipement])) {
                $icons[] = [
                    'icon'  => $mapping[$equipement],
                    'label' => $this->equipementsFormates()[$equipement] ?? $equipement,
                ];
            }
        }

        return $icons;
    }

    /**
     * Plan de salle
     */
    public function aPlanSalle(): bool
    {
        return $this->salle->organisationEmplacement !== null;
    }

    public function nombreRangees(): int
    {
        return $this->salle->organisationEmplacement->nbLignes();
    }

    public function nombreSiegesParRangee(): int
    {
        return $this->salle->organisationEmplacement->nbColonnes();
    }

    public function dispositionSieges(): string
    {
        if (! $this->aPlanSalle()) {
            return 'Disposition non définie';
        }

        return sprintf(
            '%d rangées × %d sièges',
            $this->nombreRangees(),
            $this->nombreSiegesParRangee()
        );
    }

    /**
     * Statistiques
     */
    public function tauxOccupationMoyen(): float
    {
        // TODO: Calculer depuis les réservations
        return 0.0;
    }

    public function nombreSeancesAujourdhui(): int
    {
        // TODO: Calculer depuis les séances
        return 0;
    }

    public function prochainSeance(): ?string
    {
        // TODO: Récupérer la prochaine séance
        return null;
    }

    /**
     * Tarification
     */
    public function supplementTarif(): float
    {
        $qualites      = $this->salle->qualitesProjectionSupportees;
        $supplementMax = 0.0;

        foreach ($qualites as $qualite) {
            $supplement = match ($qualite) {
                QualiteProjection::IMAX       => 5.0,
                QualiteProjection::QuatreDX   => 7.0,
                QualiteProjection::DolbyAtmos => 3.0,
                QualiteProjection::ScreenX    => 4.0,
                QualiteProjection::ICE        => 6.0,
                default                       => 0.0,
            };

            $supplementMax = max($supplementMax, $supplement);
        }

        return $supplementMax;
    }

    public function supplementTarifFormate(): string
    {
        $supplement = $this->supplementTarif();
        if ($supplement === 0.0) {
            return 'Pas de supplément';
        }

        return sprintf('+%.2f€', $supplement);
    }

    /**
     * Cinéma associé
     */
    public function cinemaId(): string
    {
        return $this->salle->cinemaId->uuid;
    }

    public function nomCinema(): string
    {
        // TODO: Récupérer depuis la relation
        return 'Cinéma';
    }

    /**
     * Actions et liens
     */
    public function lienDetail(): string
    {
        return route('admin.salles.show', $this->id());
    }

    public function lienModifier(): string
    {
        return route('admin.salles.edit', $this->id());
    }

    public function lienSupprimer(): string
    {
        return route('admin.salles.destroy', $this->id());
    }

    public function lienSeances(): string
    {
        return route('gestion.supervision.seances.index', ['salle' => $this->id()]);
    }

    /**
     * Permissions
     */
    public function peutAccueillirSeance(): bool
    {
        return $this->estActive();
    }

    public function peutEtreModifiee(): bool
    {
        return $this->salle->etat !== EtatSalle::FERMEE;
    }

    public function peutEtreSupprimee(): bool
    {
        // Ne peut être supprimée que si fermée et sans séances futures
        return $this->salle->etat === EtatSalle::FERMEE;
    }

    /**
     * Messages d'état
     */
    public function messageEtat(): string
    {
        return match ($this->salle->etat) {
            EtatSalle::ACTIVE        => 'La salle est opérationnelle',
            EtatSalle::MAINTENANCE   => 'Maintenance en cours - Retour prévu prochainement',
            EtatSalle::HORS_SERVICE  => 'Salle temporairement indisponible',
            EtatSalle::EN_RENOVATION => 'Rénovation en cours pour améliorer votre expérience',
            EtatSalle::FERMEE        => 'Salle définitivement fermée',
        };
    }
}
