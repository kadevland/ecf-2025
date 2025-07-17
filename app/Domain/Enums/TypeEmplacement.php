<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TypeEmplacement: string
{
    case Siege     = 'siege';
    case Vide      = 'vide';
}
