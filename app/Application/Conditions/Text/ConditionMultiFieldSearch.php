<?php

declare(strict_types=1);

namespace App\Application\Conditions\Text;

use App\Application\Conditions\ConditionInterface;

final readonly class ConditionMultiFieldSearch implements ConditionInterface
{
    /**
     * @param  array<string>  $fields
     */
    private function __construct(
        public array $fields,
        public string $value
    ) {}

    /**
     * @param  array<string>  $fields
     */
    public static function create(array $fields, string $value): self
    {
        return new self($fields, $value);
    }
}
