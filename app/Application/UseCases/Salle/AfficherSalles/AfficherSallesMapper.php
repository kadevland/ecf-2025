<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\AfficherSalles;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\SalleCollection;
use App\Domain\Contracts\Repositories\Salle\SalleCriteria;
use App\Domain\Enums\EtatSalle;
use App\Domain\ValueObjects\Cinema\CinemaId;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

final readonly class AfficherSallesMapper
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
    public function mapToRequest(array $data): AfficherSallesRequest
    {
        /** @var AfficherSallesRequest */
        return $this->mapper->map(AfficherSallesRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherSallesRequest $request): SalleCriteria
    {
        // Convertir string cinema_id en CinemaId
        $cinemaId = null;

        /*if ($request->cinema_id !== null) {
            $cinemaId = CinemaId::fromUuid($request->cinema_id);
        }*/

        // Convertir string etat en enum
        $etatEnum = null;
        if ($request->etat !== null) {
            $etatEnum = EtatSalle::from($request->etat);
        }

        return new SalleCriteria(
            recherche: $request->recherche,
            cinemaId: $request->cinema_id,
            etat: $etatEnum,
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
        SalleCollection $salles,
        SalleCriteria $criteria
    ): AfficherSallesResponse {
        $total      = count($salles); // TODO: vraie pagination avec count séparé
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherSallesResponse(
            salles: $salles,
            criteria: $criteria,
            pagination: $pagination,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Salle\Salle>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        SalleCriteria $criteria
    ): AfficherSallesResponse {
        /** @var SalleCollection $salles */
        $salles = $paginatedResult->items;

        return new AfficherSallesResponse(
            salles: $salles,
            criteria: $criteria,
            pagination: $paginatedResult->pagination,
        );
    }
}
