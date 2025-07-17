<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\CinemaCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;
use App\Domain\Contracts\Repositories\Cinema\CinemaRepositoryInterface;
use App\Domain\Entities\Cinema\Cinema;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Cinema\CinemaCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Cinema\CinemaEntityMapper;
use App\Models\Cinema as CinemaModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class EloquentCinemaRepository extends EloquentRepository implements CinemaRepositoryInterface
{
    public function __construct(
        CinemaModel $model,
        CinemaEntityMapper $entityMapper,
        private readonly CinemaCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(CinemaCriteria $criteria): CinemaCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, CinemaModel> $cinemas */
        $cinemas = $query->get();

        $collection = new CinemaCollection();
        $cinemas->each(function (CinemaModel $model) use ($collection): void {
            /** @var Cinema $cinema */
            if ($cinema = $this->toEntity($model)) {
                $collection->add($cinema);
            }

        });

        return $collection;
    }

    public function findPaginatedByCriteria(CinemaCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance (évite COUNT coûteux)
        $perPage = $criteria->perPage ?? 15;
        $page    = $criteria->page    ?? 1;
        // $paginator = $query->fastPaginate(perPage: $perPage, page: $page);
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);
        //

        // Mapper vers Collection domain
        $collection = new CinemaCollection();
        $paginator->each(function (CinemaModel $model) use ($collection): void {
            /** @var Cinema $cinema */
            if ($cinema = $this->toEntity($model)) {
                $collection->add($cinema);
            }

        });
        // dd();

        // Créer PaginationInfo depuis Eloquent Paginator
        $pagination = PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return PaginatedCollection::create($collection, $pagination);
    }

    public function findById(CinemaId $id): ?Cinema
    {
        $model = CinemaModel::where('uuid', $id->uuid)->first();

        if (! $model) {
            return null;
        }

        /** @var Cinema */
        return $this->toEntity($model);
    }

    public function save(Cinema $cinema): Cinema
    {
        return DB::transaction(function () use ($cinema): Cinema {
            $model = $cinema->id->isNew()
                ? /** @var CinemaModel */ $this->toModel($cinema)
                : $this->updateExistingModel($cinema);

            $model->save();

            /** @var Cinema */
            return $this->toEntity($model);
        });
    }

    protected function builder(): Builder
    {
        return parent::builder()
            ->with(['salles:id,uuid,cinema_id']);
    }

    private function updateExistingModel(Cinema $cinema): CinemaModel
    {
        /** @var CinemaModel $model */
        $model = CinemaModel::where('uuid', $cinema->id->uuid)->first()
            ?? /** @var CinemaModel */ $this->toModel($cinema);

        // Update model properties from entity
        $updatedModel = /** @var CinemaModel */ $this->toModel($cinema);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
