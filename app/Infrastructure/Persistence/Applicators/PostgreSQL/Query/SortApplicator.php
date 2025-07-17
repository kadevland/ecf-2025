<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Query;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Query\ConditionSort;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionSort>
 */
final class SortApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionSort  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        /** @var ConditionSort $condition */
        return $query->orderBy($condition->field, $condition->direction->value);
    }
}
