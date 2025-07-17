<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories\Traits;

use App\Application\Conditions\ConditionsCollection;
use App\Infrastructure\Persistence\Applicators\MongoConditionsApplicator;
use App\Infrastructure\Persistence\Applicators\PostgresConditionsApplicator;
use Illuminate\Database\Eloquent\Builder;

trait AppliesCinephoriaConditions
{
    protected function applyConditions(Builder $query, ConditionsCollection $conditions): Builder
    {
        $applicator = new PostgresConditionsApplicator();

        return $applicator->apply($query, $conditions);
    }

    protected function applyConditionsToMongo($query, ConditionsCollection $conditions)
    {
        $applicator = new MongoConditionsApplicator();

        return $applicator->apply($query, $conditions);
    }
}
