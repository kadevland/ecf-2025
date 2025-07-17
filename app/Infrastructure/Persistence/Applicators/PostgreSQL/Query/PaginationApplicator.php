<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Query;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Query\ConditionPagination;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionPagination>
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
        /** @var ConditionPagination $condition */
        $offset = ($condition->page - 1) * $condition->perPage;

        return $query->offset($offset)->limit($condition->perPage);
    }
}
