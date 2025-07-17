<?php

declare(strict_types=1);

namespace App\Application\Conditions\Set;

use App\Application\Conditions\ConditionFieldBase;
use BackedEnum;

final readonly class ConditionIn extends ConditionFieldBase
{
    private function __construct(
        string $field,
        /** @var array<mixed> */
        public array $values,
    ) {
        parent::__construct($field, $values);
    }

    /**
     * @param  array<mixed>|string|int|float|BackedEnum  $values
     */
    public static function create(string $field, array|string|int|float|BackedEnum $values): self
    {
        $arrayValues  = is_array($values) ? $values : [$values];
        $uniqueValues = array_unique($arrayValues);

        return new self($field, $uniqueValues);
    }
}
