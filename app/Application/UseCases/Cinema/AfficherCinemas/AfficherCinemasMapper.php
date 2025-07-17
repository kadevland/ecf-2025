<?php

declare(strict_types=1);

namespace App\Application\UseCases\Cinema\AfficherCinemas;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\CinemaCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

final readonly class AfficherCinemasMapper
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
    public function mapToRequest(array $data): AfficherCinemasRequest
    {
        /** @var AfficherCinemasRequest */
        return $this->mapper->map(AfficherCinemasRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherCinemasRequest $request): CinemaCriteria
    {
        return new CinemaCriteria(
            recherche: $request->recherche,
            operationnel: $request->operationnel,
            pays: $request->pays,
            ville: $request->ville,
            page: $request->page,
            perPage: $request->perPage,
        );
    }

    /**
     * Map Domain result to Response DTO
     */
    public function mapToResponse(
        CinemaCollection $cinemas,
        CinemaCriteria $criteria
    ): AfficherCinemasResponse {
        $total      = count($cinemas); // TODO: vraie pagination avec count séparé
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherCinemasResponse(
            cinemas: $cinemas,
            criteria: $criteria,
            pagination: $pagination,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Cinema\Cinema>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        CinemaCriteria $criteria
    ): AfficherCinemasResponse {
        /** @var CinemaCollection $cinemas */
        $cinemas = $paginatedResult->items;

        return new AfficherCinemasResponse(
            cinemas: $cinemas,
            criteria: $criteria,
            pagination: $paginatedResult->pagination,
        );
    }
}
