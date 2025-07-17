<?php

declare(strict_types=1);

namespace App\Application\Conditions;

abstract readonly class ConditionFieldBase implements ConditionInterface
{
    public function __construct(
        public string $field,
        public mixed $value,
    ) {}
}
