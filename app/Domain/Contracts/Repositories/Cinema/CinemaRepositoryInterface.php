<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Cinema;

use App\Domain\Collections\CinemaCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Entities\Cinema\Cinema;
use App\Domain\ValueObjects\Cinema\CinemaId;

interface CinemaRepositoryInterface
{
    /**
     * Récupère les cinémas selon des critères
     */
    public function findByCriteria(CinemaCriteria $criteria): CinemaCollection;

    /**
     * Récupère les cinémas avec pagination
     *
     * @return PaginatedCollection<Cinema>
     */
    public function findPaginatedByCriteria(CinemaCriteria $criteria): PaginatedCollection;

    /**
     * Trouve un cinéma par son ID
     */
    public function findById(CinemaId $id): ?Cinema;

    /**
     * Sauvegarde un cinéma
     */
    public function save(Cinema $cinema): Cinema;
}
