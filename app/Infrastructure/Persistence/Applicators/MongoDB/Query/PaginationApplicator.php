<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\MongoDB\Query;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Query\ConditionPagination;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @implements ApplicatorInterface<Model, ConditionPagination>
 */
final class PaginationApplicator implements ApplicatorInterface
{
    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionPagination  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        $offset = ($condition->page - 1) * $condition->perPage;

        return $query->skip($offset)
            ->take($condition->perPage);
    }
}
