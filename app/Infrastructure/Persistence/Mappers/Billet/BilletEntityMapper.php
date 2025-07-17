<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Billet;

use App\Domain\Entities\Billet\Billet;
use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\TypeTarif;
use App\Domain\ValueObjects\Billet\BilletId;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Reservation\ReservationId;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Billet as BilletModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

/**
 * Mapper entre les entités Billet et les modèles Eloquent
 */
final class BilletEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var BilletModel $model */
            // Charger les relations si nécessaire (garde-fou)
            $model->loadMissing([
                'reservation:id,uuid',
                'seance:id,uuid',
            ]);

            // Récupérer les relations - appels directs pour détecter les problèmes
            $reservation = $model->reservation;
            $seance      = $model->seance;

            return new Billet(
                id: BilletId::fromDatabase((int) $model->id, $model->uuid),
                reservationId: ReservationId::fromDatabase((int) $model->reservation_id, $reservation->uuid),
                seanceId: SeanceId::fromDatabase((int) $model->seance_id, $seance->uuid),
                numeroBillet: $model->numero_billet,
                place: $model->place,
                typeTarif: TypeTarif::from($model->type_tarif),
                prix: Prix::fromEuros($model->prix),
                qrCode: $model->qr_code,
                utilise: $model->utilise,
                dateUtilisation: $model->date_utilisation ? CarbonImmutable::parse($model->date_utilisation) : null,
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at),
            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Billet', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Billet $entity */
        $model = new BilletModel();

        $model->uuid             = $entity->id->uuid;
        $model->reservation_id   = $entity->reservationId->dbId;
        $model->seance_id        = $entity->seanceId->dbId;
        $model->numero_billet    = $entity->numeroBillet;
        $model->place            = $entity->place;
        $model->type_tarif       = $entity->typeTarif->value;
        $model->prix             = $entity->prix->getAmount() / 100; // Convertir centimes en euros
        $model->qr_code          = $entity->qrCode;
        $model->utilise          = $entity->utilise;
        $model->date_utilisation = $entity->dateUtilisation?->toDateTime();

        return $model;
    }
}
