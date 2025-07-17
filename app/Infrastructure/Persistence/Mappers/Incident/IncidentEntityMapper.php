<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Incident;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Incident\Incident;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Incident\IncidentId;
use App\Domain\ValueObjects\User\UserId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Incident as IncidentModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

/**
 * Mapper entre les entités Incident et les modèles Eloquent
 */
final class IncidentEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var IncidentModel $model */
            // Charger les relations si nécessaire
            $model->loadMissing([
                'cinema:id,uuid,nom',
                'salle:id,uuid,nom',
                'rapportePar:id,uuid',
                'assigneA:id,uuid',
            ]);

            // dd($model);

            return new Incident(
                id: IncidentId::fromDatabase((int) $model->id, $model->uuid),
                titre: $model->titre,
                type: $model->type,
                priorite: $model->priorite,
                statut: $model->statut,
                description: $model->description,
                cinemaId: CinemaId::fromDatabase($model->cinema->id, $model->cinema->uuid),
                rapportePar: UserId::fromDatabase($model->rapportePar->id, $model->rapportePar->uuid),
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at),
                salleId: null,
                assigneA: null,
                solutionApportee: $model->solution_apportee,
                commentairesInternes: $model->commentaires_internes,
                resolueAt: CarbonImmutable::parse($model->resolue_at),

            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Incident', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);
            dd($th);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Incident $entity */
        $model = new IncidentModel();

        $model->uuid  = $entity->id->uuid;
        $model->titre = $entity->titre;

        return $model;
    }
}
