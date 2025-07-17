<?php

declare(strict_types=1);

namespace App\Domain\Traits;

use App\Domain\Events\DomainEvent;

trait RecordsDomainEvents
{
    /** @var array<DomainEvent> */
    private array $domainEvents = [];

    /**
     * @return array<DomainEvent>
     */
    public function pullDomainEvents(): array
    {
        $events             = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * @return array<DomainEvent>
     */
    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function hasDomainEvents(): bool
    {
        return count($this->domainEvents) > 0;
    }

    protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
}
