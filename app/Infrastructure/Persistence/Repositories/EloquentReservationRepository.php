<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Collections\ReservationCollection;
use App\Domain\Contracts\Repositories\Reservation\ReservationCriteria;
use App\Domain\Contracts\Repositories\Reservation\ReservationRepositoryInterface;
use App\Domain\Entities\Reservation\Reservation;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Reservation\ReservationCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Reservation\ReservationEntityMapper;
use App\Models\Reservation as ReservationModel;
use Illuminate\Support\Facades\DB;

final class EloquentReservationRepository extends EloquentRepository implements ReservationRepositoryInterface
{
    public function __construct(
        ReservationModel $model,
        private readonly ReservationEntityMapper $entityMapper,
        private readonly ReservationCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(ReservationCriteria $criteria): ReservationCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, ReservationModel> $reservations */
        $reservations = $query->get();

        $collection = new ReservationCollection();
        $reservations->each(function (ReservationModel $model) use ($collection): void {
            /** @var Reservation $reservation */
            if ($reservation = $this->toEntity($model)) {
                $collection->add($reservation);
            }
        });

        return $collection;
    }

    public function findPaginatedByCriteria(ReservationCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        $collection = new ReservationCollection();
        $paginator->each(function (ReservationModel $model) use ($collection): void {
            /** @var Reservation $reservation */
            if ($reservation = $this->toEntity($model)) {
                $collection->add($reservation);
            }
        });

        $pagination = PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return PaginatedCollection::create($collection, $pagination);
    }

    public function findById(ReservationId $id): ?Reservation
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var Reservation */
        return $this->toEntity($model);
    }

    public function save(Reservation $reservation): Reservation
    {
        return DB::transaction(function () use ($reservation): Reservation {
            $model = $reservation->id->isNew()
                ? /** @var ReservationModel */ $this->toModel($reservation)
                : $this->updateExistingModel($reservation);

            $model->save();

            /** @var Reservation */
            return $this->toEntity($model);
        });
    }

    protected function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::builder()
            ->with([
                'user:id,uuid,email',
                'seance:id,uuid,film_id,salle_id,date_heure_debut,prix_base',
                'seance.film:id,uuid,titre,duree_minutes',
                'seance.salle:id,uuid,cinema_id,numero,nom',
                'billets:id,uuid,reservation_id,place',
            ]);
    }

    private function updateExistingModel(Reservation $reservation): ReservationModel
    {
        /** @var ReservationModel $model */
        $model = ReservationModel::where('uuid', $reservation->id->uuid)->first()
            ?? /** @var ReservationModel */ $this->toModel($reservation);

        // Update model properties from entity
        $updatedModel = /** @var ReservationModel */ $this->toModel($reservation);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
