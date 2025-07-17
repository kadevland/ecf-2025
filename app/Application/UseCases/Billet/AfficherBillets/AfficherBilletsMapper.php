<?php

declare(strict_types=1);

namespace App\Application\UseCases\Billet\AfficherBillets;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\BilletCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Billet\BilletCriteria;
use App\Domain\Enums\TypeTarif;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

/**
 * Mapper pour AfficherBilletsUseCase
 */
final readonly class AfficherBilletsMapper
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
    public function mapToRequest(array $data): AfficherBilletsRequest
    {
        // Convertir le type de tarif string en enum si présent
        if (isset($data['typeTarif']) && is_string($data['typeTarif']) && $data['typeTarif'] !== '') {
            $data['typeTarif'] = TypeTarif::from($data['typeTarif']);
        }

        // Convertir utilise string en boolean si présent
        if (isset($data['utilise']) && is_string($data['utilise'])) {
            $data['utilise'] = $data['utilise'] === '1' || $data['utilise'] === 'true';
        }

        /** @var AfficherBilletsRequest */
        return $this->mapper->map(AfficherBilletsRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherBilletsRequest $request): BilletCriteria
    {
        return new BilletCriteria(
            recherche: $request->recherche,
            reservationId: $request->reservationId,
            seanceId: $request->seanceId,
            typeTarif: $request->typeTarif,
            utilise: $request->utilise,
            dateUtilisationFrom: $request->dateUtilisationFrom,
            dateUtilisationTo: $request->dateUtilisationTo,
            page: $request->page,
            perPage: $request->perPage,
            sortBy: $request->sortBy,
            sortDirection: $request->sortDirection,
        );
    }

    /**
     * Map Domain result to Response DTO
     */
    public function mapToResponse(
        BilletCollection $billets,
        BilletCriteria $criteria
    ): AfficherBilletsResponse {
        $total      = count($billets); // TODO: vraie pagination avec count séparé
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherBilletsResponse(
            billets: $billets,
            criteria: $criteria,
            pagination: $pagination,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Billet\Billet>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        BilletCriteria $criteria
    ): AfficherBilletsResponse {
        /** @var BilletCollection $billets */
        $billets = $paginatedResult->items;

        return new AfficherBilletsResponse(
            billets: $billets,
            criteria: $criteria,
            pagination: $paginatedResult->pagination,
        );
    }
}
