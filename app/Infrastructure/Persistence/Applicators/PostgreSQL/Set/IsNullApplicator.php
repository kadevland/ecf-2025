<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Set;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Set\ConditionIsNull;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionIsNull>
 */
final class IsNullApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionIsNull  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        return $query->whereNull($condition->field);
    }
}
