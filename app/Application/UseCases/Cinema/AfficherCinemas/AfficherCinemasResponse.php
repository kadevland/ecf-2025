<?php

declare(strict_types=1);

namespace App\Application\UseCases\Cinema\AfficherCinemas;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\CinemaCollection;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;

final readonly class AfficherCinemasResponse
{
    public function __construct(
        public CinemaCollection $cinemas,
        public ?CinemaCriteria $criteria = null,
        public ?PaginationInfo $pagination = null,
    ) {}
}
