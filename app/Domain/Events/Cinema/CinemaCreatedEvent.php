<?php

declare(strict_types=1);

namespace App\Domain\Events\Cinema;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Commun\Adresse;

final readonly class CinemaCreatedEvent extends DomainEvent
{
    public function __construct(
        public CinemaId $cinemaId,
        public string $nom,
        public Adresse $adresse
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'cinema.created';
    }
}
