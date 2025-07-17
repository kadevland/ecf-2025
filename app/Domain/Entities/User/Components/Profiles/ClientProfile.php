<?php

declare(strict_types=1);

namespace App\Domain\Entities\User\Components\Profiles;

use InvalidArgumentException;
use App\Domain\Enums\UserType;
use App\Domain\Entities\User\Components\UserProfile;

final class ClientProfile extends UserProfile
{
    public readonly UserType   $userType;


    private function __construct(
        string $firstName,
        string $lastName,
        public private(set) ?string $phone = null
    ) {

        parent::__construct($firstName, $lastName);

        $this->userType = UserType::Client;

        if ($this->phone !== null) {
            $this->validatePhone($this->phone);
        }
    }

    public static function create(string $firstName, string $lastName, ?string $phone = null): self
    {
        return new self($firstName, $lastName, $phone);
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function hasPhone(): bool
    {
        return $this->phone !== null;
    }

    // === MUTATIONS ===

    public function changePhone(?string $phone): void
    {
        if ($phone !== null) {
            $this->validatePhone($phone);
        }

        $this->phone = $phone;
    }

    public function removePhone(): void
    {
        $this->phone = null;
    }

    // === BUSINESS METHODS ===

    public function getFormattedPhone(): ?string
    {
        if ($this->phone === null) {
            return null;
        }

        // Format français : +33 6 12 34 56 78
        if (str_starts_with($this->phone, '+33')) {
            return preg_replace('/(\+33)(\d)(\d{2})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4 $5 $6', $this->phone);
        }

        // Format belge : +32 4 12 34 56 78
        if (str_starts_with($this->phone, '+32')) {
            return preg_replace('/(\+32)(\d)(\d{2})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4 $5 $6', $this->phone);
        }

        return $this->phone;
    }

    // === VALIDATION ===

    private function validatePhone(string $phone): void
    {
        // Pattern pour téléphones français et belges
        $frenchPattern  = '/^(?:\+33|0)[1-9](?:[0-9]{8})$/';
        $belgianPattern = '/^(?:\+32|0)[1-9](?:[0-9]{7,8})$/';

        if (!preg_match($frenchPattern, $phone) && !preg_match($belgianPattern, $phone)) {
            throw new InvalidArgumentException("Invalid phone number format: {$phone}. Must be French (+33) or Belgian (+32) format.");
        }
    }
}
