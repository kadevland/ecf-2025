<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\User;

use DomainException;

final class UserAlreadySuspendedException extends DomainException
{
    public const CODE = 'USER_ALREADY_SUSPENDED';
}
