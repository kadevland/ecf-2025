<?php

declare(strict_types=1);

namespace App\Domain\Events\Film;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Film\FilmId;
use Carbon\CarbonImmutable;

final readonly class FilmCreatedEvent extends DomainEvent
{
    public function __construct(
        public FilmId $filmId,
        public string $titre,
        public CarbonImmutable $dateSortie
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'film.created';
    }
}
