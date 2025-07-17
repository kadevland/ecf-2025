<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\Film;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Film\Components\FilmComponents;
use App\Domain\Entities\Film\Film;
use App\Domain\Enums\CategorieFilm;
use App\Domain\ValueObjects\Film\FilmId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\Film as FilmModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

final class FilmEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var FilmModel $model */

            return new Film(
                id: FilmId::fromDatabase((int) $model->id, $model->uuid),
                titre: $model->titre,
                synopsis: $model->description ?? '',
                dureeMinutes: $model->duree_minutes,
                categorie: CategorieFilm::from($model->categorie->value),
                categories: $this->mapCategoriesToDomain($model->categorie->value),
                realisateur: $model->realisateur,
                acteurs: $this->mapActeursToDomain($model->acteurs ?? []),
                genres: $this->mapGenresToDomain($model->acteurs ?? []), // TODO: corriger quand le champ genres existera
                qualitesDisponibles: $model->qualites_projection ?? [],
                components: FilmComponents::vide(),
                dateSortie: $model->date_sortie ? CarbonImmutable::parse($model->date_sortie) : CarbonImmutable::now(),
                estActif: true, // TODO: mapper depuis le bon champ
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at)
            );
        } catch (Throwable $th) {

            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Film', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var Film $entity */
        $model = new FilmModel();

        $model->uuid                = $entity->id->uuid;
        $model->titre               = $entity->titre;
        $model->description         = $entity->synopsis;
        $model->duree_minutes       = $entity->dureeMinutes;
        $model->categorie           = $entity->categorie->value;
        $model->realisateur         = $entity->realisateur;
        $model->acteurs             = $this->mapActeursToModel($entity->acteurs);
        $model->qualites_projection = $entity->qualitesDisponibles;
        $model->date_sortie         = $entity->dateSortie->format('Y-m-d');

        return $model;
    }

    /**
     * @return array<CategorieFilm>
     */
    private function mapCategoriesToDomain(string $categoriePrincipale): array
    {
        return [CategorieFilm::from($categoriePrincipale)];
    }

    /**
     * @param  array<string>|null  $acteursData
     * @return array<string>
     */
    private function mapActeursToDomain(?array $acteursData): array
    {
        if (is_null($acteursData)) {
            return [];
        }

        return array_filter($acteursData, fn ($acteur) => is_string($acteur) && ! empty($acteur));
    }

    /**
     * @param  array<string>|null  $genresData
     * @return array<string>
     */
    private function mapGenresToDomain(?array $genresData): array
    {
        if (is_null($genresData)) {
            return [];
        }

        return array_filter($genresData, fn ($genre) => is_string($genre) && ! empty($genre));
    }

    /**
     * @param  array<string>  $acteurs
     * @return array<string>
     */
    private function mapActeursToModel(array $acteurs): array
    {
        return $acteurs;
    }
}
