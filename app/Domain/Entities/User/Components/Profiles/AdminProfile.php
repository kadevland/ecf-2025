<?php

declare(strict_types=1);

namespace App\Domain\Entities\User\Components\Profiles;

use App\Domain\Entities\User\Components\UserProfile;
use App\Domain\Enums\UserType;

final class AdminProfile extends UserProfile
{
    protected UserType $userType = UserType::Administrator;

    public function __construct(
        string $firstName,
        string $lastName,
        private bool $isSuperAdmin = false
    ) {
        parent::__construct($firstName, $lastName);
    }

    public static function create(string $firstName, string $lastName, bool $isSuperAdmin = false): self
    {
        return new self($firstName, $lastName, $isSuperAdmin);
    }

    public static function createSuperAdmin(string $firstName, string $lastName): self
    {
        return new self($firstName, $lastName, true);
    }

    // === GETTERS ===

    public function isSuperAdmin(): bool
    {
        return $this->isSuperAdmin;
    }

    // === MUTATIONS ===

    public function promoteToSuperAdmin(): void
    {
        $this->isSuperAdmin = true;
    }

    public function demoteFromSuperAdmin(): void
    {
        $this->isSuperAdmin = false;
    }

    // === BUSINESS METHODS ===
    // Permissions gérées par PermissionService + Casbin
}
