<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Salle\Components\OrganisationEmplacement;
use App\Domain\Entities\Salle\Salle;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Salle\NumeroSalle;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Models\Salle as SalleModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final class SalleEloquentMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): EntityInterface
    {
        /** @var SalleModel $model */

        // Charger les relations si nécessaire (garde-fou)
        $model->loadMissing([
            'cinema:id,uuid',
        ]);

        // Récupérer le cinéma pour l'UUID - appel direct pour détecter les problèmes
        $cinema = $model->cinema;

        // Créer l'organisation depuis plan_salle ou estimation
        $planSalle    = $model->plan_salle ?? [];
        $organisation = isset($planSalle['lignes'], $planSalle['colonnes'])
            ? OrganisationEmplacement::creer((int) $planSalle['lignes'], (int) $planSalle['colonnes'])
            : OrganisationEmplacement::creer(10, (int) ceil($model->capacite / 10));

        // Convertir qualité unique en array (Domain attend array)
        $qualites = $model->qualite_projection ? [$model->qualite_projection] : [QualiteProjection::Standard];

        return new Salle(
            id: SalleId::fromDatabase((int) $model->id, $model->uuid),
            numero: NumeroSalle::fromString($model->numero),
            cinemaId: CinemaId::fromDatabase((int) $model->cinema_id, $cinema->uuid),
            qualitesProjectionSupportees: $qualites,
            etat: $model->etat,
            organisationEmplacement: $organisation,
            estAccessiblePMR: $planSalle['accessible_pmr'] ?? false,
            equipements: is_array($model->equipements) ? implode(', ', $model->equipements) : $model->equipements,
            createdAt: CarbonImmutable::parse($model->created_at),
            updatedAt: CarbonImmutable::parse($model->updated_at)
        );
    }

    public function fillModelFromDomain(Model $model, EntityInterface $entity): void
    {
        /** @var SalleModel $model */
        /** @var Salle $entity */
        $model->uuid               = $entity->id->uuid;
        $model->cinema_id          = $entity->cinemaId->dbId;
        $model->numero             = $entity->numero->valeur;
        $model->nom                = $entity->numero->valeur; // Utiliser le numéro comme nom si pas de nom séparé
        $model->etat               = $entity->etat;
        $model->qualite_projection = $entity->qualitesProjectionSupportees[0] ?? QualiteProjection::Standard;

        // Capacité depuis organisation
        $model->capacite = $entity->organisationEmplacement->compterSieges();

        // Équipements
        $model->equipements = $entity->equipements ? explode(', ', $entity->equipements) : [];

        // Plan de salle
        $model->plan_salle = [
            'lignes'         => $entity->organisationEmplacement->nbLignes(),
            'colonnes'       => $entity->organisationEmplacement->nbColonnes(),
            'accessible_pmr' => $entity->estAccessiblePMR,
            'sieges'         => $entity->organisationEmplacement->obtenirMatrice(),
        ];
    }

    public function createModel(): Model
    {
        return new SalleModel();
    }
}
