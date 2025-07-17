<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Incident;

use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Query\SortDirection;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\Incident\IncidentCriteria;

/**
 * Mapper pour convertir les critères en conditions de requête
 */
final class IncidentCriteriaToConditionsMapper
{
    /**
     * Convertir les critères en conditions
     */
    public function map(IncidentCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche par titre
        if ($criteria->recherche !== null && $criteria->recherche !== '') {
            $conditions->add(ConditionSearch::create('titre', $criteria->recherche));
        }

        // Pagination
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        // if ($criteria->sort !== null && $criteria->direction !== null) {

        //     $conditions->add(ConditionSort::create($criteria->sort, $criteria->direction));
        // }

        // Tri
        if ($criteria->sortBy !== null) {
            $direction = match ($criteria->sortDirection) {
                'desc'  => SortDirection::DESC,
                default => SortDirection::ASC,
            };
            $conditions->add(ConditionSort::create($criteria->sortBy, $direction));
        }

        return $conditions;
    }
}
