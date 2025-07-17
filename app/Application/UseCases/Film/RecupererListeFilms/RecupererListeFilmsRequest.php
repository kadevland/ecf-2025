<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererListeFilms;

use App\Domain\Enums\CategorieFilm;

final readonly class RecupererListeFilmsRequest
{
    public function __construct(
        public ?CategorieFilm $categorie = null,
        public ?string $recherche = null,
        public ?string $realisateur = null,
        public ?int $dureeMin = null,
        public ?int $dureeMax = null,
        public ?string $sortBy = 'titre',
        public string $sortDirection = 'asc',
        public int $limit = 20,
        public int $offset = 0,
        public bool $seulementAffiche = false,
        public bool $avecSeances = false,
    ) {}

    public static function tous(): self
    {
        return new self();
    }

    public static function aLaffiche(): self
    {
        return new self(seulementAffiche: true, avecSeances: true);
    }

    public static function parCategorie(CategorieFilm $categorie): self
    {
        return new self(categorie: $categorie);
    }

    public static function recherche(string $terme): self
    {
        return new self(recherche: $terme);
    }
}
