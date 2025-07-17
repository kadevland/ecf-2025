<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Seance;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Seance\Seance;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Commun\Prix;
use App\Domain\ValueObjects\Film\FilmId;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Domain\ValueObjects\Seance\SeanceHoraire;
use App\Domain\ValueObjects\Seance\SeanceId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Seance as SeanceModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

final class SeanceEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var SeanceModel $model */

            // Charger les relations si nécessaire (garde-fou)
            $model->loadMissing([
                'film:id,uuid',
                'salle:id,uuid,cinema_id',
                'salle.cinema:id,uuid',
            ]);

            // Récupérer les relations pour les UUIDs - appels directs pour détecter les problèmes
            $film   = $model->film;
            $salle  = $model->salle;
            $cinema = $salle->cinema;

            // Créer SeanceHoraire depuis les dates
            $debut            = CarbonImmutable::parse($model->date_heure_debut);
            $fin              = CarbonImmutable::parse($model->date_heure_fin);
            $dureeFilmMinutes = (int) $film->duree_minutes;

            $seanceHoraire = SeanceHoraire::fromDebutEtDuree(
                $debut,
                $dureeFilmMinutes,
                15 // Temps inter-séance par défaut
            );

            // Créer Prix depuis float
            $prix = Prix::fromEuros($model->prix_base);

            // Calculer les places totales
            $placesTotal = $model->places_disponibles + $model->places_reservees;

            return new Seance(
                id: SeanceId::fromDatabase((int) $model->id, $model->uuid),
                filmId: FilmId::fromDatabase((int) $model->film_id, $film->uuid),
                cinemaId: CinemaId::fromDatabase((int) $cinema->id, $cinema->uuid),
                salleId: SalleId::fromDatabase((int) $model->salle_id, $salle->uuid),
                seanceHoraire: $seanceHoraire,
                qualiteProjection: $this->determinerQualiteProjection($model),
                prixBase: $prix,
                nombrePlacesTotal: $placesTotal,
                nombrePlacesDisponibles: $model->places_disponibles,
                nombrePlacesPmrTotal: (int) ceil($placesTotal * 0.02), // 2% PMR par défaut
                nombrePlacesPmrDisponibles: (int) ceil($model->places_disponibles * 0.02),
                etat: $model->etat,
                notes: $this->formatNotes($model),
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at)
            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Seance', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Seance $entity */
        $model = new SeanceModel();

        $model->uuid               = $entity->id->uuid;
        $model->film_id            = $entity->filmId->dbId;
        $model->salle_id           = $entity->salleId->dbId;
        $model->date_heure_debut   = $entity->seanceHoraire->debut()->toDateTime();
        $model->date_heure_fin     = $entity->seanceHoraire->fin()->toDateTime();
        $model->etat               = $entity->etat;
        $model->places_disponibles = $entity->nombrePlacesDisponibles;
        $model->places_reservees   = $entity->nombrePlacesTotal - $entity->nombrePlacesDisponibles;
        $model->prix_base          = $entity->prixBase->montant / 100; // Convertir centimes en euros
        $model->qualite_projection = $entity->qualiteProjection->value;
        $model->version            = $this->extraireVersion($entity);
        $model->sous_titres        = $this->aSousTitres($entity);
        $model->langue_audio       = $this->extraireLangueAudio($entity);
        $model->tarifs_speciaux    = $this->extraireTarifsSpeciaux($entity);

        return $model;
    }

    private function determinerQualiteProjection(SeanceModel $model): QualiteProjection
    {
        if ($model->qualite_projection) {
            return QualiteProjection::from($model->qualite_projection->value);
        }

        $version = mb_strtoupper($model->version ?? '');

        if (str_contains($version, 'IMAX')) {
            return QualiteProjection::IMAX;
        }

        if (str_contains($version, '4K') || str_contains($version, 'UHD')) {
            return QualiteProjection::UHD4K;
        }

        if (str_contains($version, '3D')) {
            return QualiteProjection::Projection3D;
        }

        return QualiteProjection::Standard;
    }

    private function formatNotes(SeanceModel $model): ?string
    {
        $notes = [];

        if ($model->version) {
            $notes[] = "Version: {$model->version}";
        }

        if ($model->sous_titres) {
            $notes[] = 'Sous-titres: Oui';
        }

        if ($model->langue_audio) {
            $notes[] = "Langue audio: {$model->langue_audio}";
        }

        if ($model->tarifs_speciaux) {
            $notes[] = 'Tarifs spéciaux: '.json_encode($model->tarifs_speciaux);
        }

        return empty($notes) ? null : implode(' | ', $notes);
    }

    private function extraireVersion(Seance $entity): ?string
    {
        $notes = $entity->notes ?? '';

        if (preg_match('/Version:\s*([^|]+)/', $notes, $matches)) {
            return mb_trim($matches[1]);
        }

        return match ($entity->qualiteProjection) {
            QualiteProjection::IMAX         => 'IMAX',
            QualiteProjection::UHD4K        => '4K',
            QualiteProjection::Projection3D => '3D',
            default                         => 'VF'
        };
    }

    private function aSousTitres(Seance $entity): bool
    {
        $notes = $entity->notes ?? '';

        return str_contains($notes, 'Sous-titres: Oui');
    }

    private function extraireLangueAudio(Seance $entity): ?string
    {
        $notes = $entity->notes ?? '';

        if (preg_match('/Langue audio:\s*([^|]+)/', $notes, $matches)) {
            return mb_trim($matches[1]);
        }

        return 'français';
    }

    private function extraireTarifsSpeciaux(Seance $entity): ?array
    {
        $notes = $entity->notes ?? '';

        if (preg_match('/Tarifs spéciaux:\s*(\{[^}]+\})/', $notes, $matches)) {
            return json_decode($matches[1], true);
        }

        return null;
    }
}
