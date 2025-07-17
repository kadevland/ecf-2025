<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Salle\Elements;

use App\Support\UrlBuilder;

final readonly class SalleSearchElementView
{
    /** @var array<int, int> */
    public const PER_PAGE_OPTIONS = [1, 15, 25, 50, 100];

    public const PER_PAGE_DEFAULT = 15;

    public function __construct(
        public readonly string $recherche,
        public readonly ?string $cinema_id,
        public readonly ?string $etat,
        public readonly int $perPage,
    ) {}

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        return self::PER_PAGE_OPTIONS;
    }

    /**
     * @return array<string, string>
     */
    public function etatOptions(): array
    {
        return [
            ''              => 'Tous les états',
            'active'        => 'Active',
            'maintenance'   => 'Maintenance',
            'hors_service'  => 'Hors service',
            'en_renovation' => 'En rénovation',
            'fermee'        => 'Fermée',
        ];
    }

    /**
     * Vérifie si le formulaire contient des données de recherche
     */
    public function isNotEmpty(): bool
    {
        return $this->recherche !== ''
            || $this->cinema_id !== null
            || $this->etat !== null
            || $this->perPage !== self::PER_PAGE_DEFAULT;
    }

    /**
     * URL pour réinitialiser le formulaire
     */
    public function resetUrl(): string
    {
        return UrlBuilder::current()
            ->only([])
            ->toString();
    }
}
