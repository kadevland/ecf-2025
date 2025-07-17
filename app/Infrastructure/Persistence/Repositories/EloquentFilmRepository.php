<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\FilmCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Film\FilmCriteria;
use App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface;
use App\Domain\Entities\Film\Film;
use App\Domain\ValueObjects\Film\FilmId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Film\FilmCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Film\FilmEntityMapper;
use App\Models\Film as FilmModel;
use Illuminate\Support\Facades\DB;

final class EloquentFilmRepository extends EloquentRepository implements FilmRepositoryInterface
{
    public function __construct(
        FilmModel $model,
        FilmEntityMapper $entityMapper,
        private readonly FilmCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(FilmCriteria $criteria): FilmCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, FilmModel> $films */
        $films = $query->get();

        $collection = new FilmCollection();
        $films->each(function (FilmModel $model) use ($collection): void {
            /** @var Film $film */
            if ($film = $this->toEntity($model)) {
                $collection->add($film);
            }

        });

        return $collection;
    }

    public function findPaginatedByCriteria(FilmCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance (évite COUNT coûteux)
        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        // Mapper vers Collection domain
        $collection = new FilmCollection();
        $paginator->each(function (FilmModel $model) use ($collection): void {
            /** @var Film $film */
            if ($film = $this->toEntity($model)) {
                $collection->add($film);
            }

        });

        // Créer PaginationInfo depuis Eloquent Paginator
        $pagination = PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return PaginatedCollection::create($collection, $pagination);
    }

    public function findById(FilmId $id): ?Film
    {
        $model = FilmModel::where('uuid', $id->uuid)->first();

        if (! $model) {
            return null;
        }

        /** @var Film */
        return $this->toEntity($model);
    }

    public function save(Film $film): Film
    {
        return DB::transaction(function () use ($film): Film {
            $model = $film->id->isNew()
                ? /** @var FilmModel */ $this->toModel($film)
                : $this->updateExistingModel($film);

            $model->save();

            /** @var Film */
            return $this->toEntity($model);
        });
    }

    private function updateExistingModel(Film $film): FilmModel
    {
        /** @var FilmModel $model */
        $model = FilmModel::where('uuid', $film->id->uuid)->first()
            ?? /** @var FilmModel */ $this->toModel($film);

        // Update model properties from entity
        $updatedModel = /** @var FilmModel */ $this->toModel($film);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
