<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\CreerFilm;

use App\Domain\Enums\CategorieFilm;

final readonly class CreerFilmRequest
{
    public function __construct(
        public string $titre,
        public string $synopsis,
        public int $dureeMinutes,
        public CategorieFilm $categorie,
        public ?string $realisateur = null,
        public ?array $acteursPrincipaux = null,
        public ?string $datesSortie = null,
        public ?array $qualitesProjection = null,
        public ?string $trailerUrl = null,
        public ?array $images = null,
        public ?array $revuesPresse = null,
    ) {}
}
