<?php

declare(strict_types=1);

namespace App\Domain\Entities\User\Components;

use App\Domain\Entities\ComponentEntity\ComponentEntity;
use App\Domain\Enums\UserType;
use Respect\Validation\Validator as v;

abstract class UserProfile implements ComponentEntity
{
    public readonly UserType $userType;

    protected function __construct(
        public private(set) string $firstName,
        public private(set) string $lastName
    ) {
        $this->validateBasicProfile();
    }

    final public function firstName(): string
    {
        return $this->firstName;
    }

    final public function lastName(): string
    {
        return $this->lastName;
    }

    final public function userType(): UserType
    {
        return $this->userType;
    }

    final public function obtenirNomComplet(): string
    {
        return mb_trim($this->firstName.' '.$this->lastName);
    }

    final public function obtenirInitiales(): string
    {
        $firstInitial = mb_strtoupper(mb_substr($this->firstName, 0, 1));
        $lastInitial  = mb_strtoupper(mb_substr($this->lastName, 0, 1));

        return $firstInitial.$lastInitial;
    }

    final public function updateFirstName(string $firstName): void
    {
        $this->validateFirstName($firstName);
        $this->firstName = $firstName;
    }

    final public function updateLastName(string $lastName): void
    {
        $this->validateLastName($lastName);
        $this->lastName = $lastName;
    }

    final public function changeName(string $firstName, string $lastName): void
    {
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);

        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }

    private function validateBasicProfile(): void
    {
        $this->validateFirstName($this->firstName);
        $this->validateLastName($this->lastName);
    }

    private function validateFirstName(string $firstName): void
    {
        v::stringType()->notEmpty()
            ->length(1, 100)
            ->assert($firstName);
    }

    private function validateLastName(string $lastName): void
    {
        v::stringType()->notEmpty()
            ->length(1, 100)
            ->assert($lastName);
    }
}
