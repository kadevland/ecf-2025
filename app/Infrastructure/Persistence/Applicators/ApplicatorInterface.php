<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators;

use App\Application\Conditions\ConditionInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TCondition of ConditionInterface
 */
interface ApplicatorInterface
{
    /**
     * @return self<TModel, TCondition>
     */
    public static function init(): self;

    /**
     * @param  Builder<TModel>  $query
     * @param  TCondition  $condition
     * @return Builder<TModel>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder;
}
