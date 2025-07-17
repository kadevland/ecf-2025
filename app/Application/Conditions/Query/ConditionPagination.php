<?php

declare(strict_types=1);

namespace App\Application\Conditions\Query;

use App\Application\Conditions\ConditionInterface;

final readonly class ConditionPagination implements ConditionInterface
{
    public const DEFAULT_PAGE = 1;

    public const DEFAULT_PER_PAGE = 15;

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $perPage = self::DEFAULT_PER_PAGE,
    ) {}

    public static function create(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_PER_PAGE): self
    {
        return new self($page, $perPage);
    }
}
