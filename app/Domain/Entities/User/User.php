<?php

declare(strict_types=1);

namespace App\Domain\Entities\User;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\User\Components\Profiles\AdminProfile;
use App\Domain\Entities\User\Components\Profiles\ClientProfile;
use App\Domain\Entities\User\Components\Profiles\EmployeeProfile;
use App\Domain\Entities\User\Components\UserProfile;
use App\Domain\Enums\UserStatus;
use App\Domain\Enums\UserType;
use App\Domain\Exceptions\User\UserAlreadySuspendedException;
use App\Domain\Exceptions\User\UserAlreadyVerifiedException;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\User\UserId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

final class User implements EntityInterface
{
    public function __construct(
        public private(set) UserId $id,
        public private(set) Email $email,
        public private(set) UserType $userType,
        public private(set) UserStatus $statut,
        public private(set) UserProfile $profile,
        public private(set) ?CarbonImmutable $emailVerifiedAt,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt
    ) {}

    // === FACTORY METHODS ===

    public static function creer(
        UserId $id,
        Email $email,
        UserType $userType,
        UserProfile $profile
    ): self {
        $now = CarbonImmutable::now();

        return new self(
            id: $id,
            email: $email,
            userType: $userType,
            statut: UserStatus::PendingVerification,
            profile: $profile,
            emailVerifiedAt: null,
            createdAt: $now,
            updatedAt: $now
        );
    }

    // === BUSINESS METHODS ===

    public function changerEmail(Email $nouvelEmail): void
    {
        if ($this->email->equals($nouvelEmail)) {
            return;
        }

        $this->email           = $nouvelEmail;
        $this->emailVerifiedAt = null; // Reset verification
        $this->statut          = UserStatus::PendingVerification;
        $this->touch();
    }

    public function changerProfile(UserProfile $nouveauProfile): void
    {
        if (! $this->peutChangerProfile($nouveauProfile)) {
            throw new InvalidArgumentException(
                'Le type de profil ne correspond pas au type d\'utilisateur'
            );
        }

        $this->profile = $nouveauProfile;
        $this->touch();
    }

    public function verifierEmail(): void
    {
        if ($this->estEmailVerifie()) {
            throw new UserAlreadyVerifiedException("L'utilisateur {$this->id->uuid} est déjà vérifié");
        }

        $this->emailVerifiedAt = CarbonImmutable::now();
        $this->statut          = UserStatus::Active;
        $this->touch();
    }

    public function suspendre(): void
    {
        if ($this->estSuspendu()) {
            throw new UserAlreadySuspendedException("L'utilisateur {$this->id->uuid} est déjà suspendu");
        }

        $this->statut = UserStatus::Suspended;
        $this->touch();
    }

    public function activer(): void
    {
        if (! $this->estEmailVerifie()) {
            throw new InvalidArgumentException('L\'email doit être vérifié avant l\'activation');
        }

        $this->statut = UserStatus::Active;
        $this->touch();
    }

    public function desactiver(): void
    {
        $this->statut = UserStatus::PendingVerification;
        $this->touch();
    }

    // === QUERY METHODS ===

    public function estEmailVerifie(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function estActif(): bool
    {
        return $this->statut === UserStatus::Active;
    }

    public function estSuspendu(): bool
    {
        return $this->statut === UserStatus::Suspended;
    }

    public function estInactif(): bool
    {
        return $this->statut === UserStatus::PendingVerification;
    }

    public function estEnAttenteVerification(): bool
    {
        return $this->statut === UserStatus::PendingVerification;
    }

    public function estClient(): bool
    {
        return $this->userType === UserType::Client;
    }

    public function estEmploye(): bool
    {
        return $this->userType === UserType::Employee;
    }

    public function estAdministrateur(): bool
    {
        return $this->userType === UserType::Administrator;
    }

    public function peutSeConnecter(): bool
    {
        return $this->estActif() && $this->estEmailVerifie();
    }

    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->id->equals($other->id);
    }

    // === PRIVATE METHODS ===

    private function peutChangerProfile(UserProfile $nouveauProfile): bool
    {
        return match ($this->userType) {
            UserType::Client        => $nouveauProfile instanceof ClientProfile,
            UserType::Employee      => $nouveauProfile instanceof EmployeeProfile,
            UserType::Administrator => $nouveauProfile instanceof AdminProfile,
        };
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
