<?php

declare(strict_types=1);

namespace App\Domain\Entities;

interface EntityInterface
{
    public function equals(self $other): bool;
}
