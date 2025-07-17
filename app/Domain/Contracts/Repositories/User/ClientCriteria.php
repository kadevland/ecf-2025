<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\User;

use Carbon\CarbonImmutable;

/**
 * Critères de recherche spécifiques aux clients
 * Force le type Client dans toutes les recherches
 */
final readonly class ClientCriteria
{
    public function __construct(
        public ?string $recherche = null,
        public ?CarbonImmutable $dateCreationDebut = null,
        public ?CarbonImmutable $dateCreationFin = null,
        public ?bool $emailVerifie = null,
        public ?string $telephone = null,
        public ?string $prenom = null,
        public ?string $nom = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
    ) {}
}
