<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\AfficherFilms;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\FilmCollection;
use App\Domain\Contracts\Repositories\Film\FilmCriteria;

final readonly class AfficherFilmsResponse
{
    public function __construct(
        public FilmCollection $films,
        public ?FilmCriteria $criteria = null,
        public ?PaginationInfo $pagination = null,
    ) {}
}
