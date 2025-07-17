<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\CreerFilm;

use App\Domain\Entities\Film\Film;
use App\Domain\ValueObjects\Film\FilmId;

final readonly class CreerFilmResponse
{
    public function __construct(
        public FilmId $filmId,
        public Film $film,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public static function success(Film $film, string $message = 'Film créé avec succès'): self
    {
        return new self(
            filmId: $film->id,
            film: $film,
            success: true,
            message: $message
        );
    }

    public static function failure(string $message): self
    {
        return new self(
            filmId: FilmId::generate(),
            film: new Film(FilmId::generate(), '', '', 0, \App\Domain\Enums\CategorieFilm::ACTION),
            success: false,
            message: $message
        );
    }
}
