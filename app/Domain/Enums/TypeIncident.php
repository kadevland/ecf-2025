<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Type d'incident technique
 */
enum TypeIncident: string
{
    case Projection    = 'projection';
    case Audio         = 'audio';
    case Eclairage     = 'eclairage';
    case Climatisation = 'climatisation';
    case Securite      = 'securite';
    case Nettoyage     = 'nettoyage';
    case Equipement    = 'equipement';
    case Siege         = 'siege';
    case Autre         = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Projection    => 'Projection',
            self::Audio         => 'Audio/Son',
            self::Eclairage     => 'Éclairage',
            self::Climatisation => 'Climatisation',
            self::Securite      => 'Sécurité',
            self::Nettoyage     => 'Nettoyage',
            self::Equipement    => 'Équipement',
            self::Siege         => 'Siège',
            self::Autre         => 'Autre',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Projection    => '📽️',
            self::Audio         => '🔊',
            self::Eclairage     => '💡',
            self::Climatisation => '❄️',
            self::Securite      => '🔒',
            self::Nettoyage     => '🧹',
            self::Equipement    => '🔧',
            self::Siege         => '💺',
            self::Autre         => '⚠️',
        };
    }
}
