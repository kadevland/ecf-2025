<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\Cinema;

use DomainException;

final class CinemaNotActiveException extends DomainException
{
    public static function forOperationWhileInactive(string $operation): self
    {
        return new self("Cannot perform '{$operation}' on inactive cinema");
    }

    public static function cannotAddSalle(): self
    {
        return new self('Cannot add salle to non-operational cinema');
    }
}
