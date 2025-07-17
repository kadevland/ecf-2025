<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Salle;

use App\Domain\Enums\EtatSalle;

final class SalleCriteria
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?int $cinemaId = null,
        public readonly ?EtatSalle $etat = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
    ) {}
}
