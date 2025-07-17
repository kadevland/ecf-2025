<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators;

use App\Application\Conditions\Comparison\ConditionBetween;
use App\Application\Conditions\Comparison\ConditionEquals;
use App\Application\Conditions\Comparison\ConditionGreaterThan;
use App\Application\Conditions\Comparison\ConditionGreaterThanOrEqual;
use App\Application\Conditions\Comparison\ConditionLessThan;
use App\Application\Conditions\Comparison\ConditionLessThanOrEqual;
use App\Application\Conditions\Comparison\ConditionNotBetween;
use App\Application\Conditions\Comparison\ConditionNotEquals;
use App\Application\Conditions\Query\ConditionPagination;
use App\Application\Conditions\Query\ConditionSort;
use App\Application\Conditions\Seance\ConditionSeanceSearch;
use App\Application\Conditions\Set\ConditionIn;
use App\Application\Conditions\Set\ConditionIsNotNull;
use App\Application\Conditions\Set\ConditionIsNull;
use App\Application\Conditions\Set\ConditionNotIn;
use App\Application\Conditions\Text\ConditionEndsWith;
use App\Application\Conditions\Text\ConditionMultiFieldSearch;
use App\Application\Conditions\Text\ConditionSearch;
use App\Application\Conditions\Text\ConditionStartsWith;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\BetweenApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\EqualsApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\GreaterThanApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\GreaterThanOrEqualApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\LessThanApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\LessThanOrEqualApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\NotBetweenApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Comparison\NotEqualsApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Query\PaginationApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Query\SortApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Seance\SeanceSearchApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Set\InApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Set\IsNotNullApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Set\IsNullApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Set\NotInApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Text\EndsWithApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Text\MultiFieldSearchApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Text\SearchApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\Text\StartsWithApplicator;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\User\ClientSearchApplicator;
use App\Infrastructure\Persistence\Conditions\User\ConditionClientSearch;

final class PostgreSQLConditionApplicator extends ConditionApplicatorOrchestrator
{
    /**
     * @return string[]
     */
    public function getApplicatorMap(): array
    {
        return [
            // Comparison
            ConditionEquals::class             => EqualsApplicator::class,
            ConditionNotEquals::class          => NotEqualsApplicator::class,
            ConditionGreaterThan::class        => GreaterThanApplicator::class,
            ConditionGreaterThanOrEqual::class => GreaterThanOrEqualApplicator::class,
            ConditionLessThan::class           => LessThanApplicator::class,
            ConditionLessThanOrEqual::class    => LessThanOrEqualApplicator::class,
            ConditionBetween::class            => BetweenApplicator::class,
            ConditionNotBetween::class         => NotBetweenApplicator::class,

            // Text
            ConditionSearch::class             => SearchApplicator::class,
            ConditionMultiFieldSearch::class   => MultiFieldSearchApplicator::class,
            ConditionStartsWith::class         => StartsWithApplicator::class,
            ConditionEndsWith::class           => EndsWithApplicator::class,

            // Set
            ConditionIn::class                 => InApplicator::class,
            ConditionNotIn::class              => NotInApplicator::class,
            ConditionIsNull::class             => IsNullApplicator::class,
            ConditionIsNotNull::class          => IsNotNullApplicator::class,

            // Query
            ConditionSort::class               => SortApplicator::class,
            ConditionPagination::class         => PaginationApplicator::class,

            // Seance specific
            ConditionSeanceSearch::class       => SeanceSearchApplicator::class,

            // User specific
            ConditionClientSearch::class       => ClientSearchApplicator::class,
        ];
    }
}
