<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Incident\Elements;

/**
 * Element de vue pour le formulaire de recherche des incidents
 */
final readonly class IncidentSearchElementView
{
    public function __construct(
        public readonly string $recherche,
        public readonly int $perPage,
    ) {}

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
        return route('gestion.supervision.incidents.index');
    }

    /**
     * Vérifie si le formulaire contient des données de recherche
     */
    public function isNotEmpty(): bool
    {
        return $this->recherche !== '' || $this->perPage !== 15;
    }
}
