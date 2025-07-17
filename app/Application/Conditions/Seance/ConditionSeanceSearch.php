<?php

declare(strict_types=1);

namespace App\Application\Conditions\Seance;

use App\Application\Conditions\ConditionInterface;

final readonly class ConditionSeanceSearch implements ConditionInterface
{
    private function __construct(
        public string $value
    ) {}

    public static function create(string $value): self
    {
        return new self($value);
    }
}
