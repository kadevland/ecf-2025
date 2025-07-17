<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Salle;

use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\SalleCollection;
use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Salle\SalleId;

interface SalleRepositoryInterface
{
    public function findByCriteria(SalleCriteria $criteria): SalleCollection;

    public function findPaginatedByCriteria(SalleCriteria $criteria): PaginatedCollection;

    public function findById(SalleId $id): ?Salle;

    public function save(Salle $salle): Salle;
}
