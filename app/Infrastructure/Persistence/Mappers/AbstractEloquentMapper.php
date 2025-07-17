<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\EntityInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentMapper
{
    /**
     * Convertit un modèle Eloquent en entité du domaine
     * Retourne null si le mapping échoue pour éviter de casser l'application
     */
    abstract public function toDomainEntity(Model $model): ?EntityInterface;

    /**
     * Convertit une entité du domaine en modèle Eloquent
     */
    abstract public function toEloquentModel(EntityInterface $entity): Model;
}
