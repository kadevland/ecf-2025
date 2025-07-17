<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Billet;

use App\Application\Conditions\Boolean\ConditionBoolean;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Date\ConditionDateRange;
use App\Application\Conditions\Enum\ConditionEnum;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Query\SortDirection;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\Billet\BilletCriteria;

/**
 * Mapper pour convertir les critères en conditions de requête
 */
final class BilletCriteriaToConditionsMapper
{
    /**
     * Convertir les critères en conditions
     */
    public function map(BilletCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche par numéro de billet ou place
        if ($criteria->recherche !== null && $criteria->recherche !== '') {
            $conditions->add(ConditionSearch::create('numero_billet', $criteria->recherche));
            $conditions->add(ConditionSearch::create('place', $criteria->recherche));
        }

        // Filtrer par réservation
        if ($criteria->reservationId !== null && $criteria->reservationId !== '') {
            $conditions->add(ConditionSearch::create('reservation.uuid', $criteria->reservationId));
        }

        // Filtrer par séance
        if ($criteria->seanceId !== null && $criteria->seanceId !== '') {
            $conditions->add(ConditionSearch::create('seance.uuid', $criteria->seanceId));
        }

        // Filtrer par type de tarif
        if ($criteria->typeTarif !== null) {
            $conditions->add(ConditionEnum::create('type_tarif', $criteria->typeTarif->value));
        }

        // Filtrer par statut d'utilisation
        if ($criteria->utilise !== null) {
            $conditions->add(ConditionBoolean::create('utilise', $criteria->utilise));
        }

        // Filtrer par date d'utilisation
        if ($criteria->dateUtilisationFrom !== null || $criteria->dateUtilisationTo !== null) {
            $conditions->add(ConditionDateRange::create(
                'date_utilisation',
                $criteria->dateUtilisationFrom,
                $criteria->dateUtilisationTo
            ));
        }

        // Pagination
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        // Tri
        if ($criteria->sortBy !== null) {
            $direction = match ($criteria->sortDirection) {
                'desc'  => SortDirection::DESC,
                default => SortDirection::ASC,
            };
            $conditions->add(ConditionSort::create($criteria->sortBy, $direction));
        } else {
            // Tri par défaut : plus récents en premier
            $conditions->add(ConditionSort::desc('created_at'));
        }

        return $conditions;
    }
}
