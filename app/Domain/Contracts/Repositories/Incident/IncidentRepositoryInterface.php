<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Incident;

use App\Domain\Collections\IncidentCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Entities\Incident\Incident;
use App\Domain\ValueObjects\Incident\IncidentId;

/**
 * Interface pour le repository des incidents
 */
interface IncidentRepositoryInterface
{
    /**
     * Trouver les incidents selon des critères
     */
    public function findByCriteria(IncidentCriteria $criteria): IncidentCollection;

    /**
     * Trouver les incidents avec pagination
     */
    public function findPaginatedByCriteria(IncidentCriteria $criteria): PaginatedCollection;

    /**
     * Trouver un incident par son ID
     */
    public function findById(IncidentId $id): ?Incident;

    /**
     * Sauvegarder un incident
     */
    public function save(Incident $incident): Incident;
}
