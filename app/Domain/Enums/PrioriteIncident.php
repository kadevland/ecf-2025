<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Priorité d'un incident
 */
enum PrioriteIncident: string
{
    case Faible   = 'faible';
    case Normale  = 'normale';
    case Elevee   = 'elevee';
    case Critique = 'critique';

    public function label(): string
    {
        return match ($this) {
            self::Faible   => 'Faible',
            self::Normale  => 'Normale',
            self::Elevee   => 'Élevée',
            self::Critique => 'Critique',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Faible   => 'badge-ghost',
            self::Normale  => 'badge-info',
            self::Elevee   => 'badge-warning',
            self::Critique => 'badge-error',
        };
    }

    public function ordre(): int
    {
        return match ($this) {
            self::Critique => 1,
            self::Elevee   => 2,
            self::Normale  => 3,
            self::Faible   => 4,
        };
    }
}
