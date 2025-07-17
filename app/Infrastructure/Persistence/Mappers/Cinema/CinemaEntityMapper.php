<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Cinema;

use App\Domain\Entities\Cinema\Cinema;
use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\Pays;
use App\Domain\Enums\StatusCinema;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Commun\Adresse;
use App\Domain\ValueObjects\Commun\CoordonneesGPS;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\Commun\Telephone;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Cinema as CinemaModel;
use App\Models\Salle;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;
use Spatie\OpeningHours\OpeningHours;
use Throwable;

final class CinemaEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var CinemaModel $model */
            $adresse           = $this->mapAdressesToDomain($model->adresse ?? []);
            $pays              = Pays::from($adresse->pays->value);
            $telephone         = $this->mapTelephonesToDomain($model->telephone ?? '', $pays);
            $horairesOuverture = $this->mapHorairesToDomain($model->horaires_ouverture ?? []);
            $salleIds          = $this->mapSalleIdsToDomain($model);
            $statut            = $model->statut ?? StatusCinema::Actif;
            $coordonneesGPS    = $this->mapCoordonneesGPSToDomain($model->coordonnees_gps ?? []);

            return new Cinema(
                id: CinemaId::fromDatabase((int) $model->id, $model->uuid),
                codeCinema: $model->code_cinema,
                nom: $model->nom,
                adresse: $adresse,
                telephone: $telephone,
                emailContact: new Email($model->email ?? 'default@cinephoria.fr'),
                horairesOuverture: $horairesOuverture,
                salleIds: $salleIds,
                statut: $statut,
                coordonneesGPS: $coordonneesGPS,
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at)
            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Cinema', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Cinema $entity */
        $model = new CinemaModel();

        $model->uuid               = $entity->id->uuid;
        $model->code_cinema        = $entity->codeCinema;
        $model->nom                = $entity->nom;
        $model->statut             = $entity->statut->value;
        $model->adresse            = $this->mapAdressesToModel($entity->adresse);
        $model->coordonnees_gps    = $this->mapCoordonneesGPSToModel($entity->coordonneesGPS);
        $model->telephone          = $entity->telephone->numero;
        $model->email              = $entity->emailContact->value;
        $model->horaires_ouverture = $entity->horairesOuverture->asStructuredData();

        return $model;
    }

    /**
     * @param  array<string, string>  $adresseData
     */
    private function mapAdressesToDomain(array $adresseData): Adresse
    {
        $paysValue = $adresseData['pays'] ?? '';

        // Gestion des deux formats : codes ISO (FR, BE) et noms complets (France, Belgique)
        $pays = match ($paysValue) {
            'France'   => Pays::France,
            'Belgique' => Pays::Belgique,
            'FR'       => Pays::France,
            'BE'       => Pays::Belgique,
            default    => Pays::from($paysValue), // Fallback avec l'enum standard
        };

        return match ($pays) {
            Pays::France   => Adresse::francaise(
                $adresseData['rue']         ?? '',
                $adresseData['code_postal'] ?? '',
                $adresseData['ville']       ?? ''
            ),
            Pays::Belgique => Adresse::belge(
                $adresseData['rue']         ?? '',
                $adresseData['code_postal'] ?? '',
                $adresseData['ville']       ?? ''
            ),
        };
    }

    private function mapTelephonesToDomain(string $telephoneValue, Pays $pays): Telephone
    {
        return match ($pays) {
            Pays::France   => Telephone::francais($telephoneValue),
            Pays::Belgique => Telephone::belge($telephoneValue),
        };
    }

    /**
     * @param  array<string, mixed>  $horairesData
     */
    private function mapHorairesToDomain(array $horairesData): OpeningHours
    {
        try {
            return ! empty($horairesData)
                ? OpeningHours::create($horairesData) // @phpstan-ignore argument.type
                : OpeningHours::create([]);
        } catch (Exception $e) {
            // Fallback avec des horaires vides si invalides
            return OpeningHours::create([]);
        }
    }

    /**
     * @return array<SalleId>
     */
    private function mapSalleIdsToDomain(CinemaModel $model): array
    {
        if (! $model->relationLoaded('salles')) {
            return [];
        }
        /** @var array<SalleId> $salleIds */
        $salleIds = $model->salles->map(function (Salle $salle): SalleId { // @phpstan-ignore argument.type
            return SalleId::fromDatabase((int) $salle->id, $salle->uuid);
        })
            ->toArray();

        return $salleIds;
    }

    /**
     * @param  array<string, float>  $coordonneesData
     */
    private function mapCoordonneesGPSToDomain(array $coordonneesData): CoordonneesGPS
    {
        return CoordonneesGPS::create(
            latitude: (float) ($coordonneesData['latitude'] ?? 48.8566),
            longitude: (float) ($coordonneesData['longitude'] ?? 2.3522)
        );
    }

    /**
     * @return array<string, float>
     */
    private function mapCoordonneesGPSToModel(CoordonneesGPS $coordonnees): array
    {
        return [
            'latitude'  => $coordonnees->latitude,
            'longitude' => $coordonnees->longitude,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function mapAdressesToModel(Adresse $adresse): array
    {
        return [
            'rue'         => $adresse->rue,
            'code_postal' => $adresse->codePostal,
            'ville'       => $adresse->ville,
            'pays'        => $adresse->pays->value,
        ];
    }
}
