<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererFilm;

use App\Domain\ValueObjects\Film\FilmId;

final readonly class RecupererFilmRequest
{
    public function __construct(
        public FilmId $filmId,
        public bool $avecDetails = true,
        public bool $avecSeances = false,
        public bool $avecImages = false,
        public bool $avecRevues = false,
    ) {}

    public static function simple(FilmId $filmId): self
    {
        return new self($filmId, avecDetails: false);
    }

    public static function complet(FilmId $filmId): self
    {
        return new self(
            $filmId,
            avecDetails: true,
            avecSeances: true,
            avecImages: true,
            avecRevues: true
        );
    }
}
