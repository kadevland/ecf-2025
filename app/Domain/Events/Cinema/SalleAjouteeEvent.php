<?php

declare(strict_types=1);

namespace App\Domain\Events\Cinema;

use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Salle\SalleId;

final readonly class SalleAjouteeEvent extends DomainEvent
{
    public function __construct(
        public CinemaId $cinemaId,
        public SalleId $salleId
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'cinema.salle_ajoutee';
    }
}
