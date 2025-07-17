<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Entities\EntityInterface;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository
{
    /**
     * Summary of builder
     *
     * @var Builder<Model>
     */
    protected Builder $builder;

    protected AbstractEloquentMapper $mapper;

    public function __construct(Model $model, AbstractEloquentMapper $mapper)
    {
        $this->builder = $model->newQuery();
        $this->mapper  = $mapper;
    }

    protected function toEntity(Model $model): ?EntityInterface
    {
        return $this->mapper->toDomainEntity($model);
    }

    protected function toModel(EntityInterface $entity): Model
    {
        return $this->mapper->toEloquentModel($entity);
    }

    /**
     * @return Builder<Model>
     */
    protected function builder(): Builder
    {
        return $this->builder->newQuery();
    }
}
