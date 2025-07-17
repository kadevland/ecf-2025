<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererFilm;

use App\Domain\Entities\Film\Film;

final readonly class RecupererFilmResponse
{
    public function __construct(
        public ?Film $film,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public static function success(Film $film): self
    {
        return new self(
            film: $film,
            success: true
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

    public static function failure(string $message): self
    {
        return new self(
            film: null,
            success: false,
            message: $message
        );
    }
}
