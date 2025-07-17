<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Types de tarifs pour les billets
 */
enum TypeTarif: string
{
    case Plein    = 'plein';
    case Reduit   = 'reduit';
    case Etudiant = 'etudiant';
    case Senior   = 'senior';
    case Enfant   = 'enfant';
    case Groupe   = 'groupe';

    /**
     * Obtenir la liste des tarifs avec libellés
     */
    public static function options(): array
    {
        return array_map(
            fn (self $tarif) => ['value' => $tarif->value, 'label' => $tarif->libelle()],
            self::cases()
        );
    }

    /**
     * Obtenir le libellé du tarif
     */
    public function libelle(): string
    {
        return match ($this) {
            self::Plein    => 'Plein tarif',
            self::Reduit   => 'Tarif réduit',
            self::Etudiant => 'Étudiant',
            self::Senior   => 'Senior',
            self::Enfant   => 'Enfant',
            self::Groupe   => 'Groupe',
        };
    }
}
