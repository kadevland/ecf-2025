<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\SalleCollection;
use App\Domain\Contracts\Repositories\Salle\SalleCriteria;
use App\Domain\Contracts\Repositories\Salle\SalleRepositoryInterface;
use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Salle\SalleCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Salle\SalleEntityMapper;
use App\Models\Salle as SalleModel;

final class EloquentSalleRepository extends EloquentRepository implements SalleRepositoryInterface
{
    public function __construct(
        SalleModel $model,
        SalleEntityMapper $entityMapper,
        private readonly SalleCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(SalleCriteria $criteria): SalleCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, SalleModel> $salles */
        $salles = $query->get();

        $collection = new SalleCollection();
        $salles->each(function (SalleModel $model) use ($collection): void {
            /** @var Salle $salle */
            if ($salle = $this->toEntity($model)) {
                $collection->add($salle);
            }
        });

        return $collection;
    }

    public function findPaginatedByCriteria(SalleCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        $collection = new SalleCollection();
        $paginator->each(function (SalleModel $model) use ($collection): void {
            /** @var Salle $salle */
            if ($salle = $this->toEntity($model)) {
                $collection->add($salle);
            }
        });

        $pagination = PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return PaginatedCollection::create($collection, $pagination);
    }

    public function findById(SalleId $id): ?Salle
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function save(Salle $salle): Salle
    {
        $model = SalleModel::where('uuid', $salle->id->uuid)->first() ?? new SalleModel();

        $this->fillModelFromEntity($model, $salle);
        $model->save();

        return $this->toEntity($model);
    }

    protected function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::builder()
            ->with(['cinema:id,uuid,nom,adresse']);
    }
}
