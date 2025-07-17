<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Film;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\Film\FilmCriteria;

final class FilmCriteriaToConditionsMapper
{
    public function map(FilmCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche globale dans le titre du film
        if ($criteria->recherche !== null) {
            $conditions->add(ConditionSearch::create('titre', $criteria->recherche));
        }

        // Filtre par catégorie
        if ($criteria->categorie !== null) {
            $conditions->add(ConditionEquals::create('categorie', $criteria->categorie->value));
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
