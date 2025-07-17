<?php

declare(strict_types=1);

namespace App\Domain\Entities\User\Components\Profiles;

use App\Domain\Entities\User\Components\UserProfile;
use App\Domain\Enums\UserType;
use App\Domain\ValueObjects\Cinema\CinemaId;
use InvalidArgumentException;
use Respect\Validation\Validator as v;

final class EmployeeProfile extends UserProfile
{
    protected UserType $userType = UserType::Employee;

    public function __construct(
        string $firstName,
        string $lastName,
        private string $employeeNumber,
        private CinemaId $cinemaId,
        private string $position
    ) {
        parent::__construct($firstName, $lastName);
        $this->validateEmployeeData();
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $employeeNumber,
        CinemaId $cinemaId,
        string $position
    ): self {
        return new self($firstName, $lastName, $employeeNumber, $cinemaId, $position);
    }

    // === GETTERS ===

    public function employeeNumber(): string
    {
        return $this->employeeNumber;
    }

    public function cinemaId(): CinemaId
    {
        return $this->cinemaId;
    }

    public function position(): string
    {
        return $this->position;
    }

    // === MUTATIONS ===

    public function changePosition(string $position): void
    {
        $this->validatePosition($position);
        $this->position = $position;
    }

    public function transferToCinema(CinemaId $cinemaId): void
    {
        $this->cinemaId = $cinemaId;
    }

    // === BUSINESS METHODS ===

    public function isManager(): bool
    {
        $managerPositions = ['manager', 'responsable', 'directeur', 'chef'];

        foreach ($managerPositions as $managerPosition) {
            if (str_contains(mb_strtolower($this->position), $managerPosition)) {
                return true;
            }
        }

        return false;
    }

    // === VALIDATION ===

    private function validateEmployeeData(): void
    {
        $this->validateEmployeeNumber($this->employeeNumber);
        $this->validatePosition($this->position);
        v::intVal()->positive()
            ->assert($this->cinemaId);
    }

    private function validateEmployeeNumber(string $employeeNumber): void
    {
        v::stringType()->notEmpty()
            ->length(3, 20)
            ->assert($employeeNumber);

        // Format attendu : EMP-001234, CIN-001, etc.
        if (! preg_match('/^[A-Z]{2,4}-\d{3,6}$/', $employeeNumber)) {
            throw new InvalidArgumentException(
                "Invalid employee number format: {$employeeNumber}. Expected format: XXX-123456"
            );
        }
    }

    private function validatePosition(string $position): void
    {
        v::stringType()->notEmpty()
            ->length(2, 100)
            ->assert($position);

        // Positions valides pour Cinéphoria
        $validPositions = [
            'accueil',
            'caisse',
            'projectionniste',
            'nettoyage',
            'sécurité',
            'manager',
            'responsable',
            'directeur',
            'technicien',
            'maintenance',
        ];

        $positionLower = mb_strtolower($position);
        $isValid       = false;

        foreach ($validPositions as $validPosition) {
            if (str_contains($positionLower, $validPosition)) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            throw new InvalidArgumentException("Invalid position: {$position}");
        }
    }
}
