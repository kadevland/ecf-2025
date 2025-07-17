<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum StatusCinema: string
{
    case Actif          = 'actif';
    case Ferme          = 'ferme';
    case En_Renovation  = 'en_renovation';
    case En_Maintenance = 'en_maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Actif          => 'actif',
            self::Ferme          => 'fermé',
            self::En_Renovation  => 'en rénovation',
            self::En_Maintenance => 'en maintenance',
        };
    }
}
