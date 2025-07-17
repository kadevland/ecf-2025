<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories\Traits;

use HiBit\Criteria\Collections\CriteriaCollection;
use HiBit\Criteria\Models\Criterion;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait AppliesCriteria
{
    protected function applyCriteria(Builder $query, CriteriaCollection $criteria): Builder
    {
        foreach ($criteria as $criterion) {
            $query = $this->applyCriterion($query, $criterion);
        }

        return $query;
    }

    private function applyCriterion(Builder $query, Criterion $criterion): Builder
    {
        return match ($criterion->operator) {
            'eq'            => $query->where($criterion->field, $criterion->value),
            'neq'           => $query->where($criterion->field, '!=', $criterion->value),
            'like'          => $query->where($criterion->field, 'like', "%{$criterion->value}%"),
            'ilike'         => $query->where($criterion->field, 'ilike', "%{$criterion->value}%"),
            'gt'            => $query->where($criterion->field, '>', $criterion->value),
            'gte'           => $query->where($criterion->field, '>=', $criterion->value),
            'lt'            => $query->where($criterion->field, '<', $criterion->value),
            'lte'           => $query->where($criterion->field, '<=', $criterion->value),
            'in'            => $query->whereIn($criterion->field, $criterion->value),
            'not_in'        => $query->whereNotIn($criterion->field, $criterion->value),
            'null'          => $query->whereNull($criterion->field),
            'not_null'      => $query->whereNotNull($criterion->field),
            'between'       => $query->whereBetween($criterion->field, $criterion->value),
            'not_between'   => $query->whereNotBetween($criterion->field, $criterion->value),
            'starts_with'   => $query->where($criterion->field, 'like', "{$criterion->value}%"),
            'ends_with'     => $query->where($criterion->field, 'like', "%{$criterion->value}"),
            'contains'      => $query->where($criterion->field, 'like', "%{$criterion->value}%"),
            'json_contains' => $query->whereJsonContains($criterion->field, $criterion->value),
            'json_length'   => $query->whereJsonLength($criterion->field, $criterion->value),
            default         => throw new InvalidArgumentException("Unsupported operator: {$criterion->operator}")
        };
    }
}
