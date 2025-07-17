<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\CreerSalle;

use App\Domain\ValueObjects\Cinema\CinemaId;

final readonly class CreerSalleRequest
{
    public function __construct(
        public CinemaId $cinemaId,
        public string $nom,
        public int $capacite,
        public string $type = 'standard',
        public ?array $equipements = null,
        public ?array $organisationEmplacements = null,
    ) {}
}
