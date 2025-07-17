<?php

declare(strict_types=1);

namespace App\Domain\Events\Seance;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Seance\SeanceId;
use Carbon\CarbonImmutable;

final readonly class SeanceCreatedEvent extends DomainEvent
{
    public function __construct(
        public SeanceId $seanceId,
        public FilmId $filmId,
        public CarbonImmutable $dateHeure
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'seance.seance_created';
    }
}
