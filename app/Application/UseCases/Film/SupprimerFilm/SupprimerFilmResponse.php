<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\SupprimerFilm;

use App\Domain\ValueObjects\Film\FilmId;

final readonly class SupprimerFilmResponse
{
    public function __construct(
        public FilmId $filmId,
        public bool $success = true,
        public ?string $message = null,
        public array $dependancesExistantes = [],
    ) {}

    public static function success(FilmId $filmId): self
    {
        return new self(
            filmId: $filmId,
            success: true,
            message: 'Film supprimé avec succès'
        );
    }

    public static function notFound(FilmId $filmId): self
    {
        return new self(
            filmId: $filmId,
            success: false,
            message: 'Film non trouvé'
        );
    }

    public static function hasDependencies(FilmId $filmId, array $dependances): self
    {
        return new self(
            filmId: $filmId,
            success: false,
            message: 'Impossible de supprimer le film car il a des dépendances',
            dependancesExistantes: $dependances
        );
    }

    public static function failure(FilmId $filmId, string $message): self
    {
        return new self(
            filmId: $filmId,
            success: false,
            message: $message
        );
    }
}
