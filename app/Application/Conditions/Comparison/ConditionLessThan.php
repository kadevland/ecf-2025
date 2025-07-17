<?php

declare(strict_types=1);

namespace App\Application\Conditions\Comparison;

use App\Application\Conditions\ConditionFieldBase;

final readonly class ConditionLessThan extends ConditionFieldBase
{
    private function __construct(string $field, mixed $value)
    {
        parent::__construct($field, $value);
    }

    public static function create(string $field, mixed $value): self
    {
        return new self($field, $value);
    }
}
