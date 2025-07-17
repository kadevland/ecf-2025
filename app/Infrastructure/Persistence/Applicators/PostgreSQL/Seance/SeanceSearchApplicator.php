<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Seance;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Seance\ConditionSeanceSearch;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionSeanceSearch>
 */
final class SeanceSearchApplicator implements ApplicatorInterface
{
    private const string OPERATOR = 'ilike';

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionSeanceSearch  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        $value = '%'.$condition->value.'%';

        return $query->where(function (Builder $q) use ($value): void {
            // Recherche dans le film lié
            $q->whereHas('film', function (Builder $query) use ($value): void {
                $query->where('titre', self::OPERATOR, $value);
            })
            // Ou dans la salle liée
                ->orWhereHas('salle', function (Builder $query) use ($value): void {
                    $query->where('nom', self::OPERATOR, $value)
                        ->orWhere('numero', self::OPERATOR, $value);
                })
            // Ou dans le cinéma lié via la salle
                ->orWhereHas('salle.cinema', function (Builder $query) use ($value): void {
                    $query->where('nom', self::OPERATOR, $value);
                });
        });
    }
}
