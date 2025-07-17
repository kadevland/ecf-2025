<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Statut d'un incident
 */
enum StatutIncident: string
{
    case Ouvert  = 'ouvert';
    case EnCours = 'en_cours';
    case Resolu  = 'resolu';
    case Ferme   = 'ferme';
    case Reporte = 'reporte';

    public function label(): string
    {
        return match ($this) {
            self::Ouvert  => 'Ouvert',
            self::EnCours => 'En cours',
            self::Resolu  => 'Résolu',
            self::Ferme   => 'Fermé',
            self::Reporte => 'Reporté',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Ouvert  => 'badge-error',
            self::EnCours => 'badge-warning',
            self::Resolu  => 'badge-success',
            self::Ferme   => 'badge-ghost',
            self::Reporte => 'badge-info',
        };
    }

    public function estActif(): bool
    {
        return in_array($this, [self::Ouvert, self::EnCours, self::Reporte], true);
    }
}
