<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Film\Components\FilmComponents;
use App\Domain\Entities\Film\Components\ImagesFilm;
use App\Domain\Entities\Film\Film;
use App\Domain\Enums\CategorieFilm;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Film\FilmId;
use App\Models\Film as FilmModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final class FilmEloquentMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): EntityInterface
    {
        /** @var FilmModel $model */

        // Reconstituer les catégories
        $categoriesPrincipale  = CategorieFilm::from($model->categorie);
        $categoriesSecondaires = array_map(
            fn ($cat) => CategorieFilm::from($cat),
            $this->jsonToArray($model->categories_secondaires)
        );
        $categories            = array_merge([$categoriesPrincipale], $categoriesSecondaires);

        // Reconstituer les qualités disponibles
        $qualitesDisponibles = array_map(
            fn ($qualite) => QualiteProjection::from($qualite),
            $this->jsonToArray($model->qualites_disponibles)
        );

        // Créer les components (pour l'instant vide, à enrichir)
        $components = FilmComponents::vide();

        // Ajouter les images si disponibles
        if ($model->affiche || $model->bande_annonce) {
            $images = ImagesFilm::vide();
            if ($model->affiche) {
                $images = $images->ajouterAffiche($model->affiche);
            }
            if ($model->bande_annonce) {
                $images = $images->ajouterBandeAnnonce($model->bande_annonce);
            }
            $components = $components->withImages($images);
        }

        return new Film(
            id: new FilmId($model->uuid),
            titre: $model->titre,
            synopsis: $model->synopsis,
            dureeMinutes: $model->duree_minutes,
            categorie: $categoriesPrincipale,
            categories: $categories,
            realisateur: $model->realisateur,
            acteurs: $this->jsonToArray($model->acteurs),
            genres: $this->jsonToArray($model->genres),
            qualitesDisponibles: $qualitesDisponibles,
            components: $components,
            dateSortie: CarbonImmutable::parse($model->date_sortie),
            estActif: $model->est_actif,
            createdAt: CarbonImmutable::parse($model->created_at),
            updatedAt: CarbonImmutable::parse($model->updated_at)
        );
    }

    public function fillModelFromDomain(Model $model, EntityInterface $entity): void
    {
        /** @var FilmModel $model */
        /** @var Film $entity */
        $model->uuid          = $entity->id->value();
        $model->titre         = $entity->titre;
        $model->synopsis      = $entity->synopsis;
        $model->duree_minutes = $entity->dureeMinutes;
        $model->date_sortie   = $entity->dateSortie->format('Y-m-d');
        $model->realisateur   = $entity->realisateur;
        $model->acteurs       = $this->nullableJson($entity->acteurs);
        $model->genres        = $this->nullableJson($entity->genres);
        $model->categorie     = $entity->categorie->value;

        // Categories secondaires (exclure la principale)
        $categoriesSecondaires         = array_map(
            fn ($cat) => $cat->value,
            $entity->getCategoriesSecondaires()
        );
        $model->categories_secondaires = $this->nullableJson($categoriesSecondaires);

        // Qualités disponibles
        $qualites                    = array_map(
            fn ($qualite) => $qualite->value,
            $entity->qualitesDisponibles
        );
        $model->qualites_disponibles = $this->nullableJson($qualites);

        $model->est_actif = $entity->estActif;

        // Extraire les données des components
        $model->affiche       = $entity->getAffichePrincipale();
        $model->bande_annonce = $entity->getBandeAnnoncePrincipale();
    }

    public function createModel(): Model
    {
        return new FilmModel();
    }
}
