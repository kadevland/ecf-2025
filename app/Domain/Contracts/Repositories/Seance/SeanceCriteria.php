<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Seance;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use Carbon\CarbonImmutable;

final class SeanceCriteria
{
    public function __construct(
        public readonly ?string $recherche = null,
        public readonly ?CinemaId $cinemaId = null,
        public readonly ?SalleId $salleId = null,
        public readonly ?FilmId $filmId = null,
        public readonly ?EtatSeance $etat = null,
        public readonly ?QualiteProjection $qualiteProjection = null,
        public readonly ?CarbonImmutable $dateDebut = null,
        public readonly ?CarbonImmutable $dateFin = null,
        public readonly ?CarbonImmutable $heureDebut = null,
        public readonly ?CarbonImmutable $heureFin = null,
        public readonly ?bool $avecPlacesDisponibles = null,
        public readonly ?int $placesMinimum = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
    ) {}
}
