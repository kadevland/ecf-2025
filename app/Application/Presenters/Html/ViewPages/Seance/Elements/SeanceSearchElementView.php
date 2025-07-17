<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Seance\Elements;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;

final readonly class SeanceSearchElementView
{
    public function __construct(
        public readonly string $recherche,
        public readonly ?string $etat,
        public readonly ?string $qualiteProjection,
        public readonly ?int $filmId,
        public readonly ?int $salleId,
        public readonly ?string $dateDebut,
        public readonly ?string $dateFin,
        public readonly int $perPage,
    ) {}

    /**
     * Options pour le select d'état
     */
    public function etatOptions(): array
    {
        $options = ['' => 'Tous les états'];

        foreach (EtatSeance::cases() as $etat) {
            $options[$etat->value] = $etat->label();
        }

        return $options;
    }

    /**
     * Options pour le select de qualité de projection
     */
    public function qualiteProjectionOptions(): array
    {
        $options = ['' => 'Toutes les qualités'];

        foreach (QualiteProjection::cases() as $qualite) {
            $options[$qualite->value] = $qualite->label();
        }

        return $options;
    }

    /**
     * Options pour le nombre d'éléments par page
     */
    public function perPageOptions(): array
    {
        return [
            15  => '15 par page',
            25  => '25 par page',
            50  => '50 par page',
            100 => '100 par page',
        ];
    }

    /**
     * URL pour le reset du formulaire
     */
    public function resetUrl(): string
    {
        return route('gestion.supervision.seances.index');
    }

    /**
     * Vérifie si le formulaire contient des données de recherche
     */
    public function isNotEmpty(): bool
    {
        return $this->recherche !== ''
            || $this->etat !== null
            || $this->qualiteProjection !== null
            || $this->filmId !== null
            || $this->salleId !== null
            || $this->dateDebut !== null
            || $this->dateFin !== null
            || $this->perPage !== 15; // Valeur par défaut
    }
}
