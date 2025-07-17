<?php

declare(strict_types=1);

namespace App\Domain\Criterias;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;

final readonly class SeanceCriteria
{
    public function __construct(
        public readonly string $recherche = '',
        public readonly ?EtatSeance $etat = null,
        public readonly ?QualiteProjection $qualiteProjection = null,
        public readonly ?int $filmId = null,
        public readonly ?int $salleId = null,
        public readonly ?string $dateDebut = null,
        public readonly ?string $dateFin = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
        public readonly ?string $sort = null,
        public readonly string $direction = 'desc',
    ) {}

    public static function fromRequest(object $request): self
    {
        return new self(
            recherche: $request->recherche ?? '',
            etat: ! empty($request->etat) ? EtatSeance::from($request->etat) : null,
            qualiteProjection: ! empty($request->qualiteProjection) ? QualiteProjection::from($request->qualiteProjection) : null,
            filmId: $request->filmId,
            salleId: $request->salleId,
            dateDebut: $request->dateDebut,
            dateFin: $request->dateFin,
            perPage: $request->perPage     ?? 15,
            page: $request->page           ?? 1,
            sort: $request->sort           ?? null,
            direction: $request->direction ?? 'desc',
        );
    }

    public function hasFilters(): bool
    {
        return $this->recherche !== ''
            || $this->etat !== null
            || $this->qualiteProjection !== null
            || $this->filmId !== null
            || $this->salleId !== null
            || $this->dateDebut !== null
            || $this->dateFin !== null;
    }
}
