<?php

declare(strict_types=1);

namespace App\Application\Conditions\Set;

use App\Application\Conditions\ConditionFieldBase;

final readonly class ConditionIsNotNull extends ConditionFieldBase
{
    private function __construct(string $field)
    {
        parent::__construct($field, true); // true pour indiquer NOT NULL
    }

    public static function create(string $field): self
    {
        return new self($field);
    }
}
