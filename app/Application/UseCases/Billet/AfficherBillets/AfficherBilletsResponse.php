<?php

declare(strict_types=1);

namespace App\Application\UseCases\Billet\AfficherBillets;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\BilletCollection;
use App\Domain\Contracts\Repositories\Billet\BilletCriteria;

/**
 * Response pour AfficherBilletsUseCase
 */
final readonly class AfficherBilletsResponse
{
    public function __construct(
        public BilletCollection $billets,
        public ?BilletCriteria $criteria = null,
        public ?PaginationInfo $pagination = null,
    ) {}
}
