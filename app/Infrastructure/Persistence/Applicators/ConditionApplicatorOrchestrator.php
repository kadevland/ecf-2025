<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators;

use App\Application\Conditions\ConditionInterface;
use App\Application\Conditions\ConditionsCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class ConditionApplicatorOrchestrator
{
    /**
     * @return array<class-string, class-string>
     */
    abstract public function getApplicatorMap(): array;

    /**
     * @param  Builder<Model>  $query
     * @param  array<class-string<ConditionInterface>>  $excludes
     * @return Builder<Model>
     */
    final public function apply(Builder $query, ConditionsCollection $conditions, array $excludes = []): Builder
    {
        return $conditions->collection()
            ->filter(fn (ConditionInterface $condition) => ! in_array(get_class($condition), $excludes, true))
            ->reduce(
                fn (Builder $query, ConditionInterface $condition) => $this->applyCondition($query, $condition),
                $query
            );
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    private function applyCondition(Builder $query, ConditionInterface $condition): Builder
    {
        $applicators     = $this->getApplicatorMap();
        $applicatorClass = $applicators[get_class($condition)];

        /** @var class-string<ApplicatorInterface<Model, ConditionInterface>> $applicatorClass */
        $applicator = $applicatorClass::init();

        return $applicator->apply($query, $condition);
    }
}
