<?php

declare(strict_types=1);

namespace App\Domain\Events\Salle;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Salle\NumeroSalle;
use App\Domain\ValueObjects\Salle\SalleId;

final readonly class SalleCreatedEvent extends DomainEvent
{
    public function __construct(
        public SalleId $salleId,
        public NumeroSalle $numero,
        public CinemaId $cinemaId
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'salle.created';
    }
}
