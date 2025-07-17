<?php

declare(strict_types=1);

namespace App\Application\Conditions\Query;

use App\Application\Conditions\ConditionInterface;

enum SortDirection: string
{
    case ASC  = 'asc';
    case DESC = 'desc';
}

final readonly class ConditionSort implements ConditionInterface
{
    private function __construct(
        public string $field,
        public SortDirection $direction,
    ) {}

    public static function create(string $field, SortDirection|string $direction = SortDirection::ASC): self
    {
        return new self($field, self::convertSortDirection($direction));
    }

    public static function asc(string $field): self
    {
        return new self($field, SortDirection::ASC);
    }

    public static function desc(string $field): self
    {
        return new self($field, SortDirection::DESC);
    }

    public static function convertSortDirection(SortDirection|string $direction): SortDirection
    {
        if ($direction instanceof SortDirection) {
            return $direction;
        }

        return (mb_strtolower($direction) === SortDirection::DESC->value) ? SortDirection::DESC : SortDirection::ASC;

    }
}
