<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum CategorieSiege: string
{
    case Standard = 'standard';
    case PMR      = 'pmr';
}
