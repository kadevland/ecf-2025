<?php

declare(strict_types=1);

namespace App\Domain\Events;

use Carbon\CarbonImmutable;

abstract readonly class DomainEvent
{
    public function __construct(
        public CarbonImmutable $occurredAt = new CarbonImmutable()
    ) {}

    abstract public function getEventName(): string;
}
