<?php

declare(strict_types=1);

namespace App\Domain\Events\Salle;

use App\Domain\Enums\EtatSalle;
use App\Domain\Events\DomainEvent;
use App\Domain\ValueObjects\Salle\SalleId;

final readonly class SalleEtatChangeEvent extends DomainEvent
{
    public function __construct(
        public SalleId $salleId,
        public EtatSalle $ancienEtat,
        public EtatSalle $nouvelEtat
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return 'salle.etat_changed';
    }
}
