<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\SupprimerFilm;

use App\Domain\ValueObjects\Film\FilmId;

final readonly class SupprimerFilmRequest
{
    public function __construct(
        public FilmId $filmId,
        public bool $forceSupprimer = false,
        public ?string $raisonSuppression = null,
    ) {}

    public static function standard(FilmId $filmId): self
    {
        return new self($filmId);
    }

    public static function force(FilmId $filmId, string $raison): self
    {
        return new self($filmId, forceSupprimer: true, raisonSuppression: $raison);
    }
}
