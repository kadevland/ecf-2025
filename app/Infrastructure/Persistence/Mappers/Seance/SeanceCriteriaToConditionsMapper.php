<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Seance;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Seance\ConditionSeanceSearch;
use App\Domain\Contracts\Repositories\Seance\SeanceCriteria;

final class SeanceCriteriaToConditionsMapper
{
    public function map(SeanceCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Recherche globale
        if ($criteria->recherche !== null && $criteria->recherche !== '') {
            $conditions->add(ConditionSeanceSearch::create($criteria->recherche));
        }

        // Filtre par film
        if ($criteria->filmId !== null) {
            $conditions->add(ConditionEquals::create('film_id', $criteria->filmId->dbId));
        }

        // Filtre par salle
        if ($criteria->salleId !== null) {
            $conditions->add(ConditionEquals::create('salle_id', $criteria->salleId->dbId));
        }

        // Filtre par état
        if ($criteria->etat !== null) {
            $conditions->add(ConditionEquals::create('etat', $criteria->etat->value));
        }

        // Filtre par qualité de projection
        if ($criteria->qualiteProjection !== null) {
            $conditions->add(ConditionEquals::create('qualite_projection', $criteria->qualiteProjection->value));
        }

        // Filtre par date de début
        if ($criteria->dateDebut !== null) {
            $conditions->add(ConditionEquals::create('date_heure_debut', $criteria->dateDebut->format('Y-m-d H:i:s')));
        }

        // Filtre par date de fin
        if ($criteria->dateFin !== null) {
            $conditions->add(ConditionEquals::create('date_heure_fin', $criteria->dateFin->format('Y-m-d H:i:s')));
        }

        // Filtre uniquement les séances avec des places disponibles
        if ($criteria->avecPlacesDisponibles === true) {
            $conditions->add(ConditionEquals::create('places_disponibles', 0));
        }

        // Filtre par nombre minimum de places
        if ($criteria->placesMinimum !== null) {
            $conditions->add(ConditionEquals::create('places_disponibles', $criteria->placesMinimum));
        }

        // Pagination moderne (page/perPage)
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        // if ($criteria->sort !== null && $criteria->direction !== null) {

        //     $conditions->add(ConditionSort::create($criteria->sort, $criteria->direction));
        // }

        return $conditions;
    }
}
