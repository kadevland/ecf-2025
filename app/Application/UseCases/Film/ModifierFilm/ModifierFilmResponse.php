<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\ModifierFilm;

use App\Domain\Entities\Film\Film;

final readonly class ModifierFilmResponse
{
    public function __construct(
        public ?Film $film,
        public bool $success = true,
        public ?string $message = null,
        public array $champsModifies = [],
    ) {}

    public static function success(Film $film, array $champsModifies = []): self
    {
        return new self(
            film: $film,
            success: true,
            message: 'Film modifié avec succès',
            champsModifies: $champsModifies
        );
    }

    public static function notFound(string $message = 'Film non trouvé'): self
    {
        return new self(
            film: null,
            success: false,
            message: $message
        );
    }

    public static function noChanges(): self
    {
        return new self(
            film: null,
            success: false,
            message: 'Aucune modification détectée'
        );
    }

    public static function failure(string $message): self
    {
        return new self(
            film: null,
            success: false,
            message: $message
        );
    }
}
