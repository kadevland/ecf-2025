<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Reservation;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;

final class ReservationCriteriaToConditionsMapper
{
    public function map(ReservationCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche par code réservation
        if ($criteria->recherche !== null && $criteria->recherche !== '') {
            $conditions->add(ConditionSearch::create('numero_reservation', $criteria->recherche));
        }

        // Filtre par statut
        if ($criteria->statut !== null) {
            $conditions->add(ConditionEquals::create('statut', $criteria->statut->value));
        }

        // Pagination
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        if ($criteria->sort !== null && $criteria->direction !== null) {

            $conditions->add(ConditionSort::create($criteria->sort, $criteria->direction));
        }

        return $conditions;
    }
}
