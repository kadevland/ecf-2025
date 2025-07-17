<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserType: string
{
    case Client        = 'client';
    case Employee      = 'employee';
    case Administrator = 'administrator';

    /**
     * Retourne tous les types sous forme de tableau
     *
     * @return array<string>
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Retourne tous les types avec leurs labels
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Retourne le label français du type d'utilisateur
     */
    public function label(): string
    {
        return match ($this) {
            self::Client        => 'Client',
            self::Employee      => 'Employé',
            self::Administrator => 'Administrateur',
        };
    }

    /**
     * Vérifie si ce type peut accéder aux fonctionnalités employé
     */
    public function canAccessEmployeeFeatures(): bool
    {
        return match ($this) {
            self::Client        => false,
            self::Employee      => true,
            self::Administrator => true,
        };
    }

    /**
     * Vérifie si ce type peut accéder aux fonctionnalités admin
     */
    public function canAccessAdminFeatures(): bool
    {
        return match ($this) {
            self::Client        => false,
            self::Employee      => false,
            self::Administrator => true,
        };
    }

    /**
     * Retourne la route de redirection par défaut après connexion
     */
    public function defaultRedirectRoute(): string
    {
        return match ($this) {
            self::Client        => '/films',
            self::Employee      => '/employee/dashboard',
            self::Administrator => '/admin/dashboard',
        };
    }
}
