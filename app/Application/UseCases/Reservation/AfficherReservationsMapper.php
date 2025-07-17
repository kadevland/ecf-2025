<?php

declare(strict_types=1);

namespace App\Application\UseCases\Reservation;

use App\Application\DTOs\PaginationInfo;
use App\Application\DTOs\Reservation\AfficherReservationsRequest;
use App\Application\DTOs\Reservation\AfficherReservationsResponse;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\ReservationCollection;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;
use App\Domain\Enums\StatutReservation;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

final readonly class AfficherReservationsMapper
{
    private TreeMapper $mapper;

    public function __construct()
    {
        $this->mapper = (new MapperBuilder())
            ->supportDateFormats('Y-m-d', 'Y-m-d H:i:s', 'Y-m-d\TH:i:s\Z')
            ->allowSuperfluousKeys()
            ->mapper();
    }

    /**
     * Map HTTP data to Request DTO
     *
     * @param  array<string, mixed>  $data
     */
    public function mapToRequest(array $data): AfficherReservationsRequest
    {
        /** @var AfficherReservationsRequest */
        return $this->mapper->map(AfficherReservationsRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherReservationsRequest $request): ReservationCriteria
    {
        $statut = null;
        if ($request->statut !== null && $request->statut !== '') {
            $statut = StatutReservation::from($request->statut);
        }

        return new ReservationCriteria(
            recherche: $request->recherche,
            statut: $statut,
            page: $request->page,
            perPage: $request->perPage,
            sort: $request->sort,
            direction: $request->direction,
        );
    }

    /**
     * Map Domain result to Response DTO
     */
    public function mapToResponse(
        ReservationCollection $reservations,
        ReservationCriteria $criteria
    ): AfficherReservationsResponse {
        $total      = count($reservations);
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherReservationsResponse(
            reservations: $reservations,
            pagination: $pagination,
            searchCriteria: $criteria,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Reservation>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        ReservationCriteria $criteria
    ): AfficherReservationsResponse {
        /** @var ReservationCollection $reservations */
        $reservations = $paginatedResult->items;

        return new AfficherReservationsResponse(
            reservations: $reservations,
            pagination: $paginatedResult->pagination,
            searchCriteria: $criteria,
        );
    }
}
