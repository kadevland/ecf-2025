<?php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Statut d'une réservation
 */
enum StatutReservation: string
{
    case EnAttente = 'en_attente';
    case Confirmee = 'confirmee';
    case Payee     = 'payee';
    case Annulee   = 'annulee';
    case Terminee  = 'terminee';
    case Expiree   = 'expiree';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Confirmee => 'Confirmée',
            self::Payee     => 'Payée',
            self::Annulee   => 'Annulée',
            self::Terminee  => 'Terminée',
            self::Expiree   => 'Expirée',
        };
    }

    public function estActive(): bool
    {
        return in_array($this, [self::EnAttente, self::Confirmee, self::Payee], true);
    }
}
