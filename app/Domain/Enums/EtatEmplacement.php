<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum EtatEmplacement: string
{
    case Disponible   = 'disponible';
    case Reserve      = 'reserve';
    case Indisponible = 'indisponible';
    case HorsService  = 'hors_service';
}
