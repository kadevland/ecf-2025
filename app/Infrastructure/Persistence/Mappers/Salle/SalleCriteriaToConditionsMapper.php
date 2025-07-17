<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Salle;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Text\ConditionMultiFieldSearch;
use App\Domain\Contracts\Repositories\Salle\SalleCriteria;

final class SalleCriteriaToConditionsMapper
{
    public function map(SalleCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche globale dans le numéro ou nom de la salle
        if ($criteria->recherche !== null) {
            $conditions->add(ConditionMultiFieldSearch::create(['numero', 'nom'], $criteria->recherche));
        }

        // Filtre par cinéma
        if ($criteria->cinemaId !== null) {
            $conditions->add(ConditionEquals::create('cinema_id', $criteria->cinemaId));
        }

        // Filtre par état
        if ($criteria->etat !== null) {
            $conditions->add(ConditionEquals::create('etat', $criteria->etat->value));
        }

        // Pagination moderne (page/perPage)
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        if ($criteria->sort !== null && $criteria->direction !== null) {

            $conditions->add(ConditionSort::create($criteria->sort, $criteria->direction));
        }

        return $conditions;
    }
}
