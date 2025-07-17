<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Text;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Text\ConditionSearch;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionSearch>
 */
final class SearchApplicator implements ApplicatorInterface
{
    private const string OPERATOR = 'ilike';

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
