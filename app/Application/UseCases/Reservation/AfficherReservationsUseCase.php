<?php

declare(strict_types=1);

namespace App\Application\UseCases\Reservation;

use App\Application\DTOs\Reservation\AfficherReservationsRequest;
use App\Application\DTOs\Reservation\AfficherReservationsResponse;
use App\Domain\Contracts\Repositories\Reservation\ReservationRepositoryInterface;

final readonly class AfficherReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private AfficherReservationsMapper $mapper,
    ) {}

    public function execute(AfficherReservationsRequest $request): AfficherReservationsResponse
    {
        $criteria = $this->mapper->mapToCriteria($request);

        // Utiliser la pagination efficace
        $paginatedResult = $this->reservationRepository->findPaginatedByCriteria($criteria);

        return $this->mapper->mapToResponseFromPaginated($paginatedResult, $criteria);
    }
}
