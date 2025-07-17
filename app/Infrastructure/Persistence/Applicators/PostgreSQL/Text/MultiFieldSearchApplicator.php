<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\Text;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\Text\ConditionMultiFieldSearch;
use App\Infrastructure\Persistence\Applicators\ApplicatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements ApplicatorInterface<\Illuminate\Database\Eloquent\Model, ConditionMultiFieldSearch>
 */
final class MultiFieldSearchApplicator implements ApplicatorInterface
{
    private const string OPERATOR = 'ilike';

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  ConditionMultiFieldSearch  $condition
     * @return Builder<Model>
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        $value  = '%'.$condition->value.'%';
        $fields = $condition->fields;

        return $query->where(function (Builder $q) use ($fields, $value): void {
            foreach ($fields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, self::OPERATOR, $value);
                } else {
                    $q->orWhere($field, self::OPERATOR, $value);
                }
            }
        });
    }
}
