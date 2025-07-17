<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum EtatSeance: string
{
    case EnCoursProgrammation = 'en_cours_programmation';
    case Programmee           = 'programmee';
    case Annulee              = 'annulee';

    public function peutPasserA(self $nouvelEtat): bool
    {
        return match ($this) {
            self::EnCoursProgrammation => $nouvelEtat === self::Programmee || $nouvelEtat === self::Annulee,
            self::Programmee           => $nouvelEtat === self::Annulee,
            self::Annulee              => false, // État final
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::EnCoursProgrammation => 'En cours de programmation',
            self::Programmee           => 'Programmée / Disponible',
            self::Annulee              => 'Annulée',
        };
    }

    /**
     * @return array<self>
     */
    public function transitionsPossibles(): array
    {
        return match ($this) {
            self::EnCoursProgrammation => [self::Programmee, self::Annulee],
            self::Programmee           => [self::Annulee],
            self::Annulee              => [],
        };
    }
}
