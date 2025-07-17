<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\MongoDB\Comparison;

use App\Application\Conditions\Comparison\ConditionLessThanOrEqual;
use App\Application\Conditions\ConditionInterface;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @implements ApplicatorInterface<Model, ConditionLessThanOrEqual>
 */
final class LessThanOrEqualApplicator implements ApplicatorInterface
{
    private const string OPERATOR = '<=';

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionLessThanOrEqual  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        return $query->where($condition->field, self::OPERATOR, $condition->value);
    }
}
