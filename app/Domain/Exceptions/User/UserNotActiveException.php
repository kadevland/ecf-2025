<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\User;

use App\Domain\Exceptions\DomainException;

final class UserNotActiveException extends DomainException
{
    public const CODE = 'USER_NOT_ACTIVE';
}
