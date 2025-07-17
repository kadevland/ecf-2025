<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\SeanceCollection;
use App\Domain\Contracts\Repositories\Seance\SeanceCriteria;
use App\Domain\Contracts\Repositories\Seance\SeanceRepositoryInterface;
use App\Domain\Entities\Seance\Seance;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Seance\SeanceCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Seance\SeanceEntityMapper;
use App\Models\Seance as SeanceModel;
use Illuminate\Support\Facades\DB;

final class EloquentSeanceRepository extends EloquentRepository implements SeanceRepositoryInterface
{
    public function __construct(
        SeanceModel $model,
        SeanceEntityMapper $entityMapper,
        private readonly SeanceCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(SeanceCriteria $criteria): SeanceCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, SeanceModel> $seances */
        $seances = $query->get();

        $collection = new SeanceCollection();
        $seances->each(function (SeanceModel $model) use ($collection): void {
            /** @var Seance $seance */
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
            }
        });

        return $collection;
    }

    public function findPaginatedByCriteria(SeanceCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance (évite COUNT coûteux)
        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        // Mapper vers Collection domain
        $collection = new SeanceCollection();
        $paginator->each(function (SeanceModel $model) use ($collection): void {
            /** @var Seance $seance */
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
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

    public function findById(SeanceId $id): ?Seance
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var Seance */
        return $this->toEntity($model);
    }

    public function save(Seance $seance): Seance
    {
        return DB::transaction(function () use ($seance): Seance {
            $model = $seance->id->isNew()
                ? /** @var SeanceModel */ $this->toModel($seance)
                : $this->updateExistingModel($seance);

            $model->save();

            /** @var Seance */
            return $this->toEntity($model);
        });
    }

    public function countByCriteria(SeanceCriteria $criteria): int
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        return $query->count();
    }

    public function findByFilmId(FilmId $filmId): SeanceCollection
    {
        $seances = $this->builder()
            ->where('film_id', $filmId->dbId)
            ->get();

        $collection = new SeanceCollection();
        $seances->each(function (SeanceModel $model) use ($collection): void {
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
            }
        });

        return $collection;
    }

    public function findBySalleId(SalleId $salleId): SeanceCollection
    {
        $seances = $this->builder()
            ->where('salle_id', $salleId->dbId)
            ->get();

        $collection = new SeanceCollection();
        $seances->each(function (SeanceModel $model) use ($collection): void {
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
            }
        });

        return $collection;
    }

    public function findByCinemaId(CinemaId $cinemaId): SeanceCollection
    {
        $seances = $this->builder()
            ->whereHas('salle', function ($query) use ($cinemaId) {
                $query->where('cinema_id', $cinemaId->dbId);
            })
            ->get();

        $collection = new SeanceCollection();
        $seances->each(function (SeanceModel $model) use ($collection): void {
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
            }
        });

        return $collection;
    }

    public function findAll(): SeanceCollection
    {
        $seances = $this->builder()->get();

        $collection = new SeanceCollection();
        $seances->each(function (SeanceModel $model) use ($collection): void {
            if ($seance = $this->toEntity($model)) {
                $collection->add($seance);
            }
        });

        return $collection;
    }

    public function delete(SeanceId $id): bool
    {
        $model = SeanceModel::where('uuid', $id->uuid)->first();

        return $model ? $model->delete() : false;
    }

    public function exists(SeanceId $id): bool
    {
        return SeanceModel::where('uuid', $id->uuid)->exists();
    }

    public function loadFilm(Seance $seance): Seance
    {
        $model = SeanceModel::with('film')->where('uuid', $seance->id->uuid)->first();

        return $model ? $this->toEntity($model) ?? $seance : $seance;
    }

    public function loadSalle(Seance $seance): Seance
    {
        $model = SeanceModel::with('salle')->where('uuid', $seance->id->uuid)->first();

        return $model ? $this->toEntity($model) ?? $seance : $seance;
    }

    public function loadReservations(Seance $seance): Seance
    {
        $model = SeanceModel::with('reservations')->where('uuid', $seance->id->uuid)->first();

        return $model ? $this->toEntity($model) ?? $seance : $seance;
    }

    protected function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::builder()
            ->with([
                'film:id,uuid,titre,duree_minutes,affiche_url',
                'salle:id,uuid,cinema_id,numero,nom,capacite',
                'salle.cinema:id,uuid,nom',
            ]);
    }

    private function updateExistingModel(Seance $seance): SeanceModel
    {
        /** @var SeanceModel $model */
        $model = SeanceModel::where('uuid', $seance->id->uuid)->first()
            ?? /** @var SeanceModel */ $this->toModel($seance);

        // Update model properties from entity
        $updatedModel = /** @var SeanceModel */ $this->toModel($seance);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
