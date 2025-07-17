<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\User;

use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\ConditionsCollection;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Text\ConditionSearch;
use App\Domain\Contracts\Repositories\User\ClientCriteria;
use App\Domain\Enums\UserType;
use App\Infrastructure\Persistence\Conditions\User\ConditionClientSearch;

final class ClientCriteriaToConditionsMapper
{
    public function map(ClientCriteria $criteria): ConditionsCollection
    {
        $conditions = new ConditionsCollection();

        // Force le type Client pour toutes les recherches
        $conditions->add(ConditionEquals::create('user_type', UserType::Client->value));

        // Recherche globale dans email, prénom, nom, téléphone
        if ($criteria->recherche !== null && $criteria->recherche !== '') {
            $conditions->add(ConditionClientSearch::create($criteria->recherche));
        }

        // Filtrage par statut
        // if ($criteria->status !== null) {
        //     $conditions->add(ConditionEquals::create('status', $criteria->status->value));
        // }

        // Filtrage par email vérifié
        if ($criteria->emailVerifie !== null) {
            if ($criteria->emailVerifie) {
                $conditions->add(ConditionEquals::create('email_verified_at', 'NOT NULL'));
            } else {
                $conditions->add(ConditionEquals::create('email_verified_at', null));
            }
        }

        // Filtrage par prénom
        if ($criteria->prenom !== null && $criteria->prenom !== '') {
            $conditions->add(ConditionSearch::create('profile->firstName', $criteria->prenom));
        }

        // Filtrage par nom
        if ($criteria->nom !== null && $criteria->nom !== '') {
            $conditions->add(ConditionSearch::create('profile->lastName', $criteria->nom));
        }

        // Filtrage par téléphone
        if ($criteria->telephone !== null && $criteria->telephone !== '') {
            $conditions->add(ConditionSearch::create('profile->phone', $criteria->telephone));
        }

        // Filtrage par date de création
        if ($criteria->dateCreationDebut !== null) {
            $conditions->add(ConditionEquals::create('created_at', '>=', $criteria->dateCreationDebut->format('Y-m-d H:i:s')));
        }

        if ($criteria->dateCreationFin !== null) {
            $conditions->add(ConditionEquals::create('created_at', '<=', $criteria->dateCreationFin->format('Y-m-d H:i:s')));
        }

        // Pagination moderne (page/perPage)
        if ($criteria->page !== null && $criteria->perPage !== null) {
            $conditions->add(ConditionPagination::create($criteria->page, $criteria->perPage));
        }

        return $conditions;
    }
}
