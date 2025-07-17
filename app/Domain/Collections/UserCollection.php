<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\User\User;
use App\Domain\Enums\UserType;
use InvalidArgumentException;

/**
 * @extends Collection<User>
 */
final class UserCollection extends Collection
{
    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->find(fn (User $user) => $user->email->value === $email);
    }

    public function filterByType(UserType $type): self
    {
        return $this->filter(fn (User $user) => $user->userType === $type);
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof User) {
            throw new InvalidArgumentException('UserCollection ne peut contenir que des instances de User');
        }
    }
}
