<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Reservation\Reservation;

/**
 * ViewModel pour les réservations conforme au template Cinema
 */
final readonly class ReservationViewModel
{
    public string $id;

    public string $numeroReservation;

    public string $statut;

    public string $nombrePlaces;

    public string $prixTotal;

    public string $codeCinema;

    public string $dateCreation;

    public string $dateExpiration;

    public string $classeBadgeStatut;

    public function __construct(
        private Reservation $reservation
    ) {
        // Direct assignment pour valeurs simples
        $this->id                = $reservation->id->uuid;
        $this->numeroReservation = $reservation->numeroReservation;
        $this->codeCinema        = $reservation->codeCinema;
        $this->statut            = \Illuminate\Support\Str::ucfirst($reservation->statut->label());

        // Fonction statique pour logique complexe
        $this->nombrePlaces      = self::formatNombrePlaces($reservation);
        $this->prixTotal         = self::formatPrix($reservation);
        $this->dateCreation      = self::formatDateCreation($reservation);
        $this->dateExpiration    = self::formatDateExpiration($reservation);
        $this->classeBadgeStatut = self::formatClasseBadgeStatut($reservation);
    }

    private static function formatNombrePlaces(Reservation $reservation): string
    {
        return $reservation->nombrePlaces.' place'.($reservation->nombrePlaces > 1 ? 's' : '');
    }

    private static function formatPrix(Reservation $reservation): string
    {
        return number_format($reservation->prixTotal->getAmount() / 100, 2, ',', ' ').' €';
    }

    private static function formatDateCreation(Reservation $reservation): string
    {
        return $reservation->createdAt->format('d/m/Y H:i');
    }

    private static function formatDateExpiration(Reservation $reservation): string
    {
        if (! $reservation->expiresAt) {
            return '-';
        }

        if ($reservation->expiresAt->isPast()) {
            return 'Expirée';
        }

        return $reservation->expiresAt->format('d/m/Y H:i');
    }

    private static function formatClasseBadgeStatut(Reservation $reservation): string
    {
        return match ($reservation->statut) {
            \App\Domain\Enums\StatutReservation::EnAttente => 'badge-warning',
            \App\Domain\Enums\StatutReservation::Confirmee => 'badge-info',
            \App\Domain\Enums\StatutReservation::Payee     => 'badge-success',
            \App\Domain\Enums\StatutReservation::Annulee   => 'badge-error',
            \App\Domain\Enums\StatutReservation::Terminee  => 'badge-ghost',
            \App\Domain\Enums\StatutReservation::Expiree   => 'badge-ghost',
        };
    }
}
