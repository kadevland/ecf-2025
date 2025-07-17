<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\RecupererListeFilms;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\FilmCollection;

final readonly class RecupererListeFilmsResponse
{
    public function __construct(
        public FilmCollection $films,
        public PaginationInfo $pagination,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public static function success(
        FilmCollection $films,
        PaginationInfo $pagination
    ): self {
        return new self(
            films: $films,
            pagination: $pagination,
            success: true
        );
    }

    public static function failure(string $message): self
    {
        return new self(
            films: new FilmCollection([]),
            pagination: PaginationInfo::empty(),
            success: false,
            message: $message
        );
    }

    public function isEmpty(): bool
    {
        return $this->films->isEmpty();
    }

    public function count(): int
    {
        return $this->films->count();
    }
}
