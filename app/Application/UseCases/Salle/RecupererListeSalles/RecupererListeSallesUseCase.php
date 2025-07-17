<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\RecupererListeSalles;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\SalleCollection;
use App\Domain\Contracts\Repositories\Salle\SalleRepositoryInterface;
use App\Domain\ValueObjects\Cinema\CinemaId;

final readonly class RecupererListeSallesRequest
{
    public function __construct(
        public ?CinemaId $cinemaId = null,
        public ?string $type = null,
        public ?int $capaciteMin = null,
        public ?int $capaciteMax = null,
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}

final readonly class RecupererListeSallesResponse
{
    public function __construct(
        public SalleCollection $salles,
        public PaginationInfo $pagination,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public static function success(SalleCollection $salles, PaginationInfo $pagination): self
    {
        return new self($salles, $pagination);
    }
}

final readonly class RecupererListeSallesUseCase
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository
    ) {}

    public function execute(RecupererListeSallesRequest $request): RecupererListeSallesResponse
    {
        // TODO: Implémenter la logique complète
        $salles     = new SalleCollection([]);
        $pagination = PaginationInfo::empty();

        return RecupererListeSallesResponse::success($salles, $pagination);
    }
}
