<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison;

use App\Application\Conditions\Comparison\ConditionNotBetween;
use App\Application\Conditions\ConditionInterface;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionNotBetween>
 */
final class NotBetweenApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionNotBetween  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        /** @var array<mixed> $values */
        $values = $condition->value;

        return $query->whereNotBetween($condition->field, $values);
    }
}
