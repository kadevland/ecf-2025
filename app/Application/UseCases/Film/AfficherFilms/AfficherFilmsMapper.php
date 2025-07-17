<?php

declare(strict_types=1);

namespace App\Application\UseCases\Film\AfficherFilms;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\FilmCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Film\FilmCriteria;
use App\Domain\Enums\CategorieFilm;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

final readonly class AfficherFilmsMapper
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
    public function mapToRequest(array $data): AfficherFilmsRequest
    {
        /** @var AfficherFilmsRequest */
        return $this->mapper->map(AfficherFilmsRequest::class, $data);
    }

    /**
     * Map Request to Domain Criteria
     */
    public function mapToCriteria(AfficherFilmsRequest $request): FilmCriteria
    {
        // Convertir string categorie en enum
        $categorieEnum = null;
        if ($request->categorie !== null) {
            $categorieEnum = CategorieFilm::from($request->categorie);
        }

        return new FilmCriteria(
            recherche: $request->recherche,
            categorie: $categorieEnum,
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
        FilmCollection $films,
        FilmCriteria $criteria
    ): AfficherFilmsResponse {
        $total      = count($films); // TODO: vraie pagination avec count séparé
        $pagination = PaginationInfo::fromPageParams($total, $criteria->page, $criteria->perPage);

        return new AfficherFilmsResponse(
            films: $films,
            criteria: $criteria,
            pagination: $pagination,
        );
    }

    /**
     * Map PaginatedCollection to Response DTO (efficace)
     *
     * @param  PaginatedCollection<\App\Domain\Entities\Film\Film>  $paginatedResult
     */
    public function mapToResponseFromPaginated(
        PaginatedCollection $paginatedResult,
        FilmCriteria $criteria
    ): AfficherFilmsResponse {
        /** @var FilmCollection $films */
        $films = $paginatedResult->items;

        return new AfficherFilmsResponse(
            films: $films,
            criteria: $criteria,
            pagination: $paginatedResult->pagination,
        );
    }
}
