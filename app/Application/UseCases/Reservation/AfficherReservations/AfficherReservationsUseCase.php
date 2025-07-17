<?php

declare(strict_types=1);

namespace App\Application\UseCases\Reservation\AfficherReservations;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;

/**
 * UseCase pour afficher la liste des réservations
 */
final readonly class AfficherReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function execute(AfficherReservationsRequest $request): AfficherReservationsResponse
    {
        $criteria = ReservationCriteria::create();

        // Appliquer les filtres
        if ($request->statut) {
            $criteria = $criteria->withStatut($request->statut);
        }

        if ($request->userId) {
            $criteria = $criteria->withUserId($request->userId);
        }

        if ($request->cinemaId) {
            $criteria = $criteria->withCinema($request->cinemaId);
        }

        if ($request->search) {
            $criteria = $criteria->withSearch($request->search);
        }

        // Tri
        $criteria = $criteria->withSort($request->sortBy, $request->sortDirection);

        // Pagination
        $criteria = $criteria->withPagination($request->limit, $request->offset);

        // Récupérer les réservations et le total
        $reservations = $this->reservationRepository->findByCriteria($criteria);
        $total        = $this->reservationRepository->countByCriteria($criteria);

        // Créer la pagination
        $pagination = PaginationInfo::fromParams($total, $request->limit, $request->offset);

        return new AfficherReservationsResponse(
            reservations: $reservations,
            pagination: $pagination
        );
    }
}
