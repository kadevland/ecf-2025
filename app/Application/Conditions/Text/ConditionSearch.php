<?php

declare(strict_types=1);

namespace App\Application\Conditions\Text;

use App\Application\Conditions\ConditionFieldBase;

final readonly class ConditionSearch extends ConditionFieldBase
{
    private function __construct(string $field, string $value)
    {
        parent::__construct($field, $value);
    }

    public static function create(string $field, string $value): self
    {
        return new self($field, $value);
    }
}
