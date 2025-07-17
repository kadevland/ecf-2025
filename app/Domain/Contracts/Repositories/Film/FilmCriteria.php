<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Film;

use App\Domain\Enums\CategorieFilm;

final class FilmCriteria
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?CategorieFilm $categorie = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
    ) {}
}
