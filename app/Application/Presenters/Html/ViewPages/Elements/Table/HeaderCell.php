<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Support\UrlBuilder;

final readonly class HeaderCell
{
    public const SORT_ASC = 'asc';

    public const SORT_DESC = 'desc';

    public function __construct(
        public readonly string $label,
        public readonly string $key,
        public readonly bool $sortable = false,
        public readonly ?string $currentSort = null,
        public readonly ?string $currentDirection = null,
    ) {}

    /**
     * Vérifie si cette colonne est actuellement triée
     */
    public function isSorted(): bool
    {
        return $this->currentSort === $this->key;
    }

    /**
     * Retourne la direction de tri actuelle pour cette colonne
     * null si cette colonne n'est pas triée
     */
    public function sortDirection(): ?string
    {
        return $this->isSorted() ? $this->currentDirection : null;
    }

    /**
     * Génère l'URL pour trier par cette colonne
     * Logique: null -> asc -> desc -> asc...
     */
    public function sortUrl(): ?string
    {
        if (! $this->sortable) {
            return null;
        }

        // Détermine la prochaine direction
        $newDirection = match ($this->sortDirection()) {
            self::SORT_ASC  => self::SORT_DESC,
            self::SORT_DESC => self::SORT_ASC,
            default         => self::SORT_ASC, // null ou autre -> commence par asc
        };

        return UrlBuilder::current()
            ->with('sort', $this->key)
            ->with('direction', $newDirection)
            ->remove('page') // Reset pagination
            ->toString();
    }

    /**
     * Retourne la classe CSS pour l'icône de tri
     */
    public function sortIconClass(): string
    {
        if (! $this->sortable) {
            return '';
        }

        return match ($this->sortDirection()) {
            self::SORT_ASC  => 'sort-asc',
            self::SORT_DESC => 'sort-desc',
            default         => 'sort-none', // pas trié
        };
    }

    /**
     * Retourne l'icône SVG path pour le tri
     */
    public function sortIcon(): string
    {
        if (! $this->sortable) {
            return '';
        }

        return match ($this->sortDirection()) {
            self::SORT_ASC  => 'M5 15l7-7 7 7',     // Flèche vers le haut
            self::SORT_DESC => 'M19 9l-7 7-7-7',   // Flèche vers le bas
            default         => 'M8 9l4-4 4 4m0 6l-4 4-4-4', // Double flèche (non trié)
        };
    }
}
