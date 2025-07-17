<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum EtatSalle: string
{
    case Active        = 'active';
    case Maintenance   = 'maintenance';
    case HorsService   = 'hors_service';
    case EnRenovation  = 'en_renovation';
    case Fermee        = 'fermee';
}
