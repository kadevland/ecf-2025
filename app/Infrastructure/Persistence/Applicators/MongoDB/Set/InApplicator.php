<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\MongoDB\Set;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Set\ConditionIn;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @implements ApplicatorInterface<Model, ConditionIn>
 */
final class InApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionIn  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        return $query->whereIn($condition->field, $condition->values);
    }
}
