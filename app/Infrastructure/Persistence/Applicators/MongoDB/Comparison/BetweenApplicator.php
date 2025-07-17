<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\MongoDB\Comparison;

use App\Application\Conditions\Comparison\ConditionBetween;
use App\Application\Conditions\ConditionInterface;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @implements ApplicatorInterface<Model, ConditionBetween>
 */
final class BetweenApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionBetween  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        /** @var array<mixed> $values */
        $values = $condition->value;

        return $query->whereBetween($condition->field, $values);
    }
}
