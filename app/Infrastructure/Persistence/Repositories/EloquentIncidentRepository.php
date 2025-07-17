<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Domain\Collections\IncidentCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Incident\IncidentCriteria;
use App\Domain\Contracts\Repositories\Incident\IncidentRepositoryInterface;
use App\Domain\Entities\Incident\Incident;
use App\Domain\ValueObjects\Incident\IncidentId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\Incident\IncidentCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\Incident\IncidentEntityMapper;
use App\Models\Incident as IncidentModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Repository Eloquent pour les incidents
 */
final class EloquentIncidentRepository extends EloquentRepository implements IncidentRepositoryInterface
{
    public function __construct(
        IncidentModel $model,
        IncidentEntityMapper $entityMapper,
        private readonly IncidentCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    /**
     * {@inheritDoc}
     */
    public function findByCriteria(IncidentCriteria $criteria): IncidentCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, IncidentModel> $models */
        $models = $query->get();

        $collection = new IncidentCollection();
        $models->each(function (IncidentModel $model) use ($collection): void {
            /** @var Incident $incident */
            if ($incident = $this->toEntity($model)) {
                $collection->add($incident);
            }
        });

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function findPaginatedByCriteria(IncidentCriteria $criteria): PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance
        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        // Mapper vers Collection domain
        $collection = new IncidentCollection();
        $paginator->each(function (IncidentModel $model) use ($collection): void {
            /** @var Incident $incident */
            if ($incident = $this->toEntity($model)) {
                $collection->add($incident);
            }
        });

        // Créer PaginationInfo depuis Eloquent Paginator
        $pagination = \App\Application\DTOs\PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return PaginatedCollection::create($collection, $pagination);
    }

    /**
     * {@inheritDoc}
     */
    public function findById(IncidentId $id): ?Incident
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var Incident $incident */
        return $this->toEntity($model);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Incident $incident): Incident
    {
        /** @var IncidentModel $model */
        $model = $incident->id->isNew()
            ? $this->toModel($incident)
            : $this->updateExistingModel($incident);

        $model->save();

        /** @var Incident */
        return $this->toEntity($model);
    }

    /**
     * Mettre à jour un modèle existant
     */
    protected function builder(): Builder
    {
        return parent::builder()
            ->with([
                'rapportePar:id,uuid',
                'assigneA:id,uuid',
                'cinema:id,uuid,nom',
                'salle:id,uuid,nom',
            ]);
    }

    private function updateExistingModel(Incident $incident): IncidentModel
    {
        /** @var IncidentModel $model */
        $model = IncidentModel::where('uuid', $incident->id->uuid)->first()
            ?? $this->toModel($incident);

        // Update model properties from entity
        $updatedModel = $this->toModel($incident);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
