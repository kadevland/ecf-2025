<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Incident\Incident;
use App\Domain\Enums\Incident\PrioriteIncident;
use App\Domain\Enums\Incident\StatutIncident;
use InvalidArgumentException;

/**
 * @extends Collection<Incident>
 */
final class IncidentCollection extends Collection
{
    public function findByTitre(string $titre): ?Incident
    {
        /** @var Incident|null */
        return $this->find(fn (Incident $incident) => $incident->titre === $titre);
    }

    public function filterByStatut(StatutIncident $statut): self
    {
        return $this->filter(fn (Incident $incident) => $incident->statut === $statut);
    }

    public function filterByPriorite(PrioriteIncident $priorite): self
    {
        return $this->filter(fn (Incident $incident) => $incident->priorite === $priorite);
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Incident) {
            throw new InvalidArgumentException('IncidentCollection ne peut contenir que des instances de Incident');
        }
    }
}
