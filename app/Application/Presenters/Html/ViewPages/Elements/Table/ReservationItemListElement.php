<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\ViewModels\ReservationViewModel;
use App\Domain\Entities\Reservation\Reservation;

final readonly class ReservationItemListElement extends ItemListElement
{
    public function __construct(
        public readonly ReservationViewModel $reservation,
        ActionListView $actions,
    ) {
        parent::__construct($actions);
    }

    /**
     * Créer un item reservation avec ses actions
     */
    public static function creer(Reservation $reservation): self
    {
        $reservationViewModel = new ReservationViewModel($reservation);

        // Créer les actions pour cette réservation
        $actions = collect([
            new Action(
                label: 'Voir',
                url: '#', // TODO: créer la route show
                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                class: 'btn-info',
            ),
            new Action(
                label: 'Billets',
                url: '#', // TODO: créer la route billets
                icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
                class: 'btn-secondary',
                tooltip: 'Voir les billets de cette réservation',
            ),
            new Action(
                label: 'Modifier',
                url: '#', // TODO: créer la route edit
                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                class: 'btn-warning',
            ),
            new Action(
                label: 'Annuler',
                url: '#', // TODO: créer la route cancel
                icon: 'M6 18L18 6M6 6l12 12',
                class: 'btn-error',
                confirmMessage: 'Êtes-vous sûr de vouloir annuler cette réservation ?',
            ),
        ]);

        $actionList = new ActionListView(
            actions: $actions,
            isDropdown: true,
            dropdownLabel: 'Actions',
        );

        return new self($reservationViewModel, $actionList);
    }

    /**
     * Affiche la valeur pour une colonne donnée
     */
    public function displayValue(string $columnKey): mixed
    {
        return property_exists($this->reservation, $columnKey) ? $this->reservation->{$columnKey} : '';
    }
}
