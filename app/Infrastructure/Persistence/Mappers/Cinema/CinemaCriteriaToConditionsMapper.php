<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Cinema;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;
use App\Domain\Enums\StatusCinema;

final class CinemaCriteriaToConditionsMapper
{
    public function map(CinemaCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche globale dans le nom du cinéma
        if ($criteria->recherche !== null) {
            $conditions->add(ConditionSearch::create('nom', $criteria->recherche));
        }

        // Mapping opérationnel → statut technique
        if ($criteria->operationnel !== null) {

            $conditions->add(ConditionEquals::create('statut', StatusCinema::Actif->value));
        }

        // Mapping pays métier → champ JSON technique
        if ($criteria->pays !== null) {
            $conditions->add(ConditionEquals::create('adresse->pays', $criteria->pays));
        }

        // Mapping ville métier → champ JSON technique
        if ($criteria->ville !== null) {
            $conditions->add(ConditionSearch::create('adresse->ville', $criteria->ville));
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
