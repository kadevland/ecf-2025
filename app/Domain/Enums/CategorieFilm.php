<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum CategorieFilm: string
{
    case Action         = 'action';
    case Aventure       = 'aventure';
    case Comedie        = 'comedie';
    case Drame          = 'drame';
    case Horreur        = 'horreur';
    case ScienceFiction = 'science_fiction';
    case Thriller       = 'thriller';
    case Romance        = 'romance';
    case Animation      = 'animation';
    case Documentaire   = 'documentaire';
    case Fantastique    = 'fantastique';
    case Historique     = 'historique';
    case Musical        = 'musical';
    case Western        = 'western';
    case Policier       = 'policier';

    public function label(): string
    {
        return match ($this) {
            self::Action         => 'Action',
            self::Aventure       => 'Aventure',
            self::Comedie        => 'Comédie',
            self::Drame          => 'Drame',
            self::Horreur        => 'Horreur',
            self::ScienceFiction => 'Science-Fiction',
            self::Thriller       => 'Thriller',
            self::Romance        => 'Romance',
            self::Animation      => 'Animation',
            self::Documentaire   => 'Documentaire',
            self::Fantastique    => 'Fantastique',
            self::Historique     => 'Historique',
            self::Musical        => 'Musical',
            self::Western        => 'Western',
            self::Policier       => 'Policier',
        };
    }
}
