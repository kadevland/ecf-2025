<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Application\DTOs\PaginationInfo;
use App\Domain\Collections\BilletCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Billet\BilletCriteria;
use App\Domain\Contracts\Repositories\Billet\BilletRepositoryInterface;
use App\Domain\Entities\Billet\Billet;
use App\Domain\ValueObjects\Billet\BilletId;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Billet\BilletCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Billet\BilletEntityMapper;
use App\Models\Billet as BilletModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Repository Eloquent pour les billets
 */
final class EloquentBilletRepository extends EloquentRepository implements BilletRepositoryInterface
{
    public function __construct(
        BilletModel $model,
        BilletEntityMapper $entityMapper,
        private readonly BilletCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    /**
     * {@inheritDoc}
     */
    public function findByCriteria(BilletCriteria $criteria): BilletCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, BilletModel> $models */
        $models = $query->get();

        $collection = new BilletCollection();
        $models->each(function (BilletModel $model) use ($collection): void {
            /** @var Billet $billet */
            if ($billet = $this->toEntity($model)) {
                $collection->add($billet);
            }
        });

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function findPaginatedByCriteria(BilletCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance
        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        // Mapper vers Collection domain
        $collection = new BilletCollection();
        $paginator->each(function (BilletModel $model) use ($collection): void {
            /** @var Billet $billet */
            if ($billet = $this->toEntity($model)) {
                $collection->add($billet);
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

    /**
     * {@inheritDoc}
     */
    public function findById(BilletId $id): ?Billet
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var Billet */
        return $this->toEntity($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findByReservationId(ReservationId $reservationId): BilletCollection
    {
        $models = $this->builder()
            ->whereHas('reservation', function ($query) use ($reservationId) {
                $query->where('uuid', $reservationId->uuid);
            })
            ->get();

        $collection = new BilletCollection();
        $models->each(function (BilletModel $model) use ($collection): void {
            /** @var Billet $billet */
            if ($billet = $this->toEntity($model)) {
                $collection->add($billet);
            }
        });

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySeanceId(SeanceId $seanceId): BilletCollection
    {
        $models = $this->builder()
            ->whereHas('seance', function ($query) use ($seanceId) {
                $query->where('uuid', $seanceId->uuid);
            })
            ->get();

        $collection = new BilletCollection();
        $models->each(function (BilletModel $model) use ($collection): void {
            /** @var Billet $billet */
            if ($billet = $this->toEntity($model)) {
                $collection->add($billet);
            }
        });

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function findByNumeroBillet(string $numeroBillet): ?Billet
    {
        $model = $this->builder()
            ->where('numero_billet', $numeroBillet)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function findByQrCode(string $qrCode): ?Billet
    {
        $model = $this->builder()
            ->where('qr_code', $qrCode)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Billet $billet): Billet
    {
        return DB::transaction(function () use ($billet): Billet {
            /** @var BilletModel $model */
            $model = $billet->id->isNew()
                ? $this->toModel($billet)
                : $this->updateExistingModel($billet);

            $model->save();

            /** @var Billet */
            return $this->toEntity($model);
        });
    }

    /**
     * Créer le builder de base avec les relations nécessaires
     */
    protected function builder(): Builder
    {
        return parent::builder()
            ->with([
                'reservation:id,uuid,numero_reservation,user_id',
                'reservation.user:id,uuid,email',
                'seance:id,uuid,film_id,salle_id,date_heure_debut,date_heure_fin',
                'seance.film:id,uuid,titre',
                'seance.salle:id,uuid,cinema_id,numero,nom',
                'seance.salle.cinema:id,uuid,nom',
            ]);
    }

    /**
     * Mettre à jour un modèle existant
     */
    private function updateExistingModel(Billet $billet): BilletModel
    {
        /** @var BilletModel $model */
        $model = BilletModel::where('uuid', $billet->id->uuid)->first()
            ?? $this->toModel($billet);

        // Update model properties from entity
        $updatedModel = $this->toModel($billet);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
