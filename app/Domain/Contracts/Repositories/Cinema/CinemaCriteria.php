<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Cinema;

final class CinemaCriteria
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?bool $operationnel = null,
        public readonly ?string $pays = null,
        public readonly ?string $ville = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
    ) {}
}
