<?php

declare(strict_types=1);

namespace App\Application\UseCases\Incident\AfficherIncidents;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\IncidentCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Incident\IncidentCriteria;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

/**
 * Mapper pour AfficherIncidentsUseCase
 */
final readonly class AfficherIncidentsMapper
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
    public function mapToRequest(array $data): AfficherIncidentsRequest
    {
        /** @var AfficherIncidentsRequest */
        return $this->mapper->map(AfficherIncidentsRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherIncidentsRequest $request): IncidentCriteria
    {
        return new IncidentCriteria(
            recherche: $request->recherche,
            page: $request->page,
            perPage: $request->perPage,
        );
    }

    /**
     * Map Domain result to Response DTO
     */
    public function mapToResponse(
        IncidentCollection $incidents,
        IncidentCriteria $criteria
    ): AfficherIncidentsResponse {
        $total      = count($incidents); // TODO: vraie pagination avec count séparé
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherIncidentsResponse(
            incidents: $incidents,
            criteria: $criteria,
            pagination: $pagination,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Incident\Incident>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        IncidentCriteria $criteria
    ): AfficherIncidentsResponse {
        /** @var IncidentCollection $incidents */
        $incidents = $paginatedResult->items;

        return new AfficherIncidentsResponse(
            incidents: $incidents,
            criteria: $criteria,
            pagination: $paginatedResult->pagination,
        );
    }
}
