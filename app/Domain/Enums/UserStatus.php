<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserStatus: string
{
    case Active              = 'active';
    case Suspended           = 'suspended';
    case PendingVerification = 'pending_verification';

    /**
     * Retourne tous les statuts sous forme de tableau
     *
     * @return array<string>
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Retourne tous les statuts avec leurs labels
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
     * Retourne le label français du statut
     */
    public function label(): string
    {
        return match ($this) {
            self::Active              => 'Actif',
            self::Suspended           => 'Suspendu',
            self::PendingVerification => 'En attente de vérification',
        };
    }

    /**
     * Vérifie si l'utilisateur peut se connecter
     */
    public function canLogin(): bool
    {
        return match ($this) {
            self::Active              => true,
            self::Suspended           => false,
            self::PendingVerification => true, // Peut se connecter mais avec restrictions
        };
    }

    /**
     * Vérifie si le statut est actif
     */
    public function isActive(): bool
    {
        return $this === self::Active;
    }

    /**
     * Vérifie si le statut nécessite une vérification
     */
    public function needsVerification(): bool
    {
        return $this === self::PendingVerification;
    }

    /**
     * Vérifie si le statut est suspendu
     */
    public function isSuspended(): bool
    {
        return $this === self::Suspended;
    }

    /**
     * Retourne la couleur CSS pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::Active              => 'green',
            self::Suspended           => 'red',
            self::PendingVerification => 'orange',
        };
    }

    /**
     * Retourne la sévérité du statut pour la logique métier
     */
    public function severity(): string
    {
        return match ($this) {
            self::Active              => 'success',
            self::Suspended           => 'danger',
            self::PendingVerification => 'warning',
        };
    }
}
