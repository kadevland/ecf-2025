<?php

declare(strict_types=1);

namespace App\Application\UseCases\Seance\AfficherSeances;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use Carbon\CarbonImmutable;

final readonly class AfficherSeancesRequest
{
    public function __construct(
        public ?string $recherche = null,
        public ?CinemaId $cinemaId = null,
        public ?SalleId $salleId = null,
        public ?FilmId $filmId = null,
        public ?EtatSeance $etat = null,
        public ?QualiteProjection $qualiteProjection = null,
        public ?CarbonImmutable $dateDebut = null,
        public ?CarbonImmutable $dateFin = null,
        public ?CarbonImmutable $heureDebut = null,
        public ?CarbonImmutable $heureFin = null,
        public ?bool $avecPlacesDisponibles = null,
        public ?int $placesMinimum = null,
        public ?int $page = null,
        public ?int $perPage = null,
    ) {
    }
}
