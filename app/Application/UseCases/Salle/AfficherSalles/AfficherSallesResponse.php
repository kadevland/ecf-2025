<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\AfficherSalles;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\SalleCollection;
use App\Domain\Contracts\Repositories\Salle\SalleCriteria;

final readonly class AfficherSallesResponse
{
    public function __construct(
        public SalleCollection $salles,
        public ?SalleCriteria $criteria = null,
        public ?PaginationInfo $pagination = null,
    ) {}
}
