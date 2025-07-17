<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\ModifierFilm;

use App\Domain\Enums\CategorieFilm;
use App\Domain\ValueObjects\Film\FilmId;

final readonly class ModifierFilmRequest
{
    public function __construct(
        public FilmId $filmId,
        public ?string $titre = null,
        public ?string $synopsis = null,
        public ?int $dureeMinutes = null,
        public ?CategorieFilm $categorie = null,
        public ?string $realisateur = null,
        public ?array $acteursPrincipaux = null,
        public ?string $datesSortie = null,
        public ?array $qualitesProjection = null,
        public ?string $trailerUrl = null,
        public ?array $images = null,
        public ?array $revuesPresse = null,
    ) {}

    public function hasChanges(): bool
    {
        return $this->titre !== null
            || $this->synopsis !== null
            || $this->dureeMinutes !== null
            || $this->categorie !== null
            || $this->realisateur !== null
            || $this->acteursPrincipaux !== null
            || $this->datesSortie !== null
            || $this->qualitesProjection !== null
            || $this->trailerUrl !== null
            || $this->images !== null
            || $this->revuesPresse !== null;
    }
}
