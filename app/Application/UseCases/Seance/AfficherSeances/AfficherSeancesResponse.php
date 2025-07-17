<?php

declare(strict_types=1);

namespace App\Application\UseCases\Seance\AfficherSeances;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Contracts\Repositories\Seance\SeanceCriteria;
use App\Domain\Entities\Seance\Seance;

final readonly class AfficherSeancesResponse
{
    /**
     * @param  Seance[]  $seances
     */
    public function __construct(
        public array $seances,
        public SeanceCriteria $criteria,
        public PaginationInfo $pagination,
    ) {}
}
