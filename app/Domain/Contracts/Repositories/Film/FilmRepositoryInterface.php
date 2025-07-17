<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Film;

use App\Domain\Collections\FilmCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Entities\Film\Film;
use App\Domain\ValueObjects\Film\FilmId;

interface FilmRepositoryInterface
{
    /**
     * Récupère les films selon des critères
     */
    public function findByCriteria(FilmCriteria $criteria): FilmCollection;

    /**
     * Récupère les films avec pagination
     *
     * @return PaginatedCollection<Film>
     */
    public function findPaginatedByCriteria(FilmCriteria $criteria): PaginatedCollection;

    /**
     * Trouve un film par son ID
     */
    public function findById(FilmId $id): ?Film;

    /**
     * Sauvegarde un film
     */
    public function save(Film $film): Film;
}
