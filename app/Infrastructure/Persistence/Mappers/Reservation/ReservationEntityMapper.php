<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Reservation;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Reservation\Reservation;
use App\Domain\Enums\StatutReservation;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Domain\ValueObjects\User\UserId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Reservation as ReservationModel;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final class ReservationEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        if (! $model instanceof ReservationModel) {
            return null;
        }

        try {
            // Charger les relations si nécessaire (garde-fou)
            $model->loadMissing([
                'user:id,uuid',
                'seance:id,uuid',
                'billets:id,uuid,place',
            ]);

            $id        = ReservationId::fromDatabase($model->id, $model->uuid);
            $userId    = UserId::fromDatabase($model->user_id, $model->user->uuid);
            $seanceId  = SeanceId::fromDatabase($model->seance_id, $model->seance->uuid);
            $statut    = StatutReservation::from($model->statut);
            $prixTotal = Prix::fromEuros($model->prix_total);

            return new Reservation(
                id: $id,
                userId: $userId,
                seanceId: $seanceId,
                statut: $statut,
                nombrePlaces: $model->nombre_places,
                prixTotal: $prixTotal,
                codeCinema: $model->code_cinema,
                numeroReservation: $model->numero_reservation,
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at),
                confirmedAt: $model->confirmed_at ? CarbonImmutable::parse($model->confirmed_at) : null,
                expiresAt: $model->expires_at ? CarbonImmutable::parse($model->expires_at) : null,
                notes: $model->notes,
            );
        } catch (Exception $e) {
            Log::error('Erreur mapping Reservation', ['error' => $e->getMessage(), 'model' => $model->id]);
            dump($e);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        if (! $entity instanceof Reservation) {
            throw new Exception('Entity must be a Reservation');
        }

        $model = new ReservationModel();

        if (! $entity->id->isNew()) {
            $model = ReservationModel::where('uuid', $entity->id->uuid)->firstOrNew();
        }

        $model->uuid               = $entity->id->uuid;
        $model->user_id            = $entity->userId->dbId;
        $model->seance_id          = $entity->seanceId->dbId;
        $model->statut             = $entity->statut->value;
        $model->nombre_places      = $entity->nombrePlaces;
        $model->prix_total         = $entity->prixTotal->getAmount() / 100; // Cents to euros
        $model->code_cinema        = $entity->codeCinema;
        $model->numero_reservation = $entity->numeroReservation;
        $model->confirmed_at       = $entity->confirmedAt?->toDateTimeString();
        $model->expires_at         = $entity->expiresAt?->toDateTimeString();
        $model->notes              = $entity->notes;

        return $model;
    }
}
