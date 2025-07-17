<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Salle;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Salle\Components\OrganisationEmplacement;
use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Salle\NumeroSalle;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Salle as SalleModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

final class SalleEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var SalleModel $model */

            // Charger les relations si nécessaire (garde-fou)
            $model->loadMissing([
                'cinema:id,uuid',
            ]);

            // Récupérer la relation cinema - appel direct pour détecter les problèmes
            $cinema = $model->cinema;

            return new Salle(
                id: SalleId::fromDatabase((int) $model->id, $model->uuid),
                numero: NumeroSalle::fromString($model->numero),
                cinemaId: CinemaId::fromDatabase((int) $model->cinema_id, $cinema->uuid),
                qualitesProjectionSupportees: $model->qualites_projection ?? [],
                etat: $model->etat,
                organisationEmplacement: OrganisationEmplacement::creer(10, 20),
                estAccessiblePMR: true, // TODO: ajouter ce champ en DB
                equipements: json_encode($model->equipements ?? []),
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at)
            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Salle', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Salle $entity */
        $model = new SalleModel();

        $model->uuid                = $entity->id->uuid;
        $model->numero              = $entity->numero->value();
        $model->cinema_id           = $entity->cinemaId->uuid;
        $model->etat                = $entity->etat;
        $model->qualites_projection = $entity->qualitesProjectionSupportees;
        $model->equipements         = json_decode($entity->equipements ?? '[]', true);

        return $model;
    }
}
