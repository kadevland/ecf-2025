<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Film\Elements;

use App\Support\UrlBuilder;

final readonly class FilmSearchElementView
{
    /** @var array<int, int> */
    public const PER_PAGE_OPTIONS = [1, 15, 25, 50, 100];

    public const PER_PAGE_DEFAULT = 15;

    public function __construct(
        public readonly string $recherche,
        public readonly ?string $categorie,
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
    public function categorieOptions(): array
    {
        return [
            ''                => 'Toutes les catégories',
            'action'          => 'Action',
            'aventure'        => 'Aventure',
            'comedie'         => 'Comédie',
            'drame'           => 'Drame',
            'horreur'         => 'Horreur',
            'science_fiction' => 'Science-Fiction',
            'thriller'        => 'Thriller',
            'romance'         => 'Romance',
            'animation'       => 'Animation',
            'documentaire'    => 'Documentaire',
            'fantastique'     => 'Fantastique',
            'historique'      => 'Historique',
            'musical'         => 'Musical',
            'western'         => 'Western',
            'policier'        => 'Policier',
        ];
    }

    /**
     * Vérifie si le formulaire contient des données de recherche
     */
    public function isNotEmpty(): bool
    {
        return $this->recherche !== ''
            || $this->categorie !== null
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
