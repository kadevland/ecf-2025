<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\AfficherFilms;

final readonly class AfficherFilmsRequest
{
    public function __construct(
        /** @var non-empty-string|null */
        public ?string $recherche = null,
        public ?string $categorie = null,
        /** @var positive-int|null */
        public ?int $page = null,
        /** @var int<1,100>|null */
        public ?int $perPage = null,
        /** @var non-empty-string|null */
        public ?string $sort = null,
        /** @var non-empty-string|null */
        public ?string $direction = null,
    ) {}
}
