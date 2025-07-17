<?php

declare(strict_types=1);

namespace App\Application\Conditions\Set;

use App\Application\Conditions\ConditionFieldBase;

final readonly class ConditionIsNull extends ConditionFieldBase
{
    private function __construct(string $field)
    {
        parent::__construct($field, null);
    }

    public static function create(string $field): self
    {
        return new self($field);
    }
}
