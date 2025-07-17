<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\MongoDB\Text;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Text\ConditionSearch;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @implements ApplicatorInterface<Model, ConditionSearch>
 */
final class SearchApplicator implements ApplicatorInterface
{
    private const string OPERATOR = 'like';

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionSearch  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        /** @var string $value */
        $value = $condition->value;

        return $query->where($condition->field, self::OPERATOR, '%'.$value.'%');
    }
}
