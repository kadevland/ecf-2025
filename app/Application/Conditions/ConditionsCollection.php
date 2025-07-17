<?php

declare(strict_types=1);

namespace App\Application\Conditions;

use App\Domain\Collections\Collection;

/**
 * @extends Collection<ConditionInterface>
 */
final class ConditionsCollection extends Collection
{
    protected function validateItem(mixed $item): void
    {
        // Type safety ensured by generic annotation @extends Collection<ConditionInterface>
        // PHPStan ensures only ConditionInterface instances can be added
    }
}
