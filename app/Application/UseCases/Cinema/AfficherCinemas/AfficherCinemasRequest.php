<?php

declare(strict_types=1);

namespace App\Application\UseCases\Cinema\AfficherCinemas;

final readonly class AfficherCinemasRequest
{
    public function __construct(
        /** @var non-empty-string|null */
        public ?string $recherche = null,
        public ?bool $operationnel = null,
        /** @var non-empty-string|null */
        public ?string $pays = null,
        /** @var non-empty-string|null */
        public ?string $ville = null,
        /** @var positive-int|null */
        public ?int $page = null,
        /** @var int<1,100>|null */
        public ?int $perPage = null,
    ) {}
}
