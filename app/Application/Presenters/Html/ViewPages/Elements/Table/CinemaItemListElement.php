<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\ViewModels\CinemaViewModel;
use App\Domain\Entities\Cinema\Cinema;

final readonly class CinemaItemListElement extends ItemListElement
{
    public function __construct(
        public readonly CinemaViewModel $cinema,
        ActionListView $actions,
    ) {
        parent::__construct($actions);
    }

    /**
     * Créer un item cinema avec ses actions
     */
    public static function creer(Cinema $cinema): self
    {
        $cinemaViewModel = new CinemaViewModel($cinema);

        // Créer les actions pour ce cinéma
        $actions = collect([
            new Action(
                label: 'Voir',
                url: '#', // TODO: créer la route show
                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                class: 'btn-info',
            ),
            new Action(
                label: 'Salles',
                url: \App\Common\Navigation::gestion()->supervision()->salles($cinema->id),
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                class: 'btn-secondary',
                tooltip: 'Voir les salles de ce cinéma',
            ),
            new Action(
                label: 'Modifier',
                url: '#', // TODO: créer la route edit
                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                class: 'btn-warning',
            ),
            new Action(
                label: 'Supprimer',
                url: '#', // TODO: créer la route destroy
                icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                class: 'btn-error',
                confirmMessage: 'Êtes-vous sûr de vouloir supprimer ce cinéma ?',
            ),
        ]);

        $actionList = new ActionListView(
            actions: $actions,
            isDropdown: true,
            dropdownLabel: 'Actions',
        );

        return new self($cinemaViewModel, $actionList);
    }

    /**
     * Affiche la valeur pour une colonne donnée
     */
    public function displayValue(string $columnKey): mixed
    {
        return property_exists($this->cinema, $columnKey) ? $this->cinema->{$columnKey} : '';
    }
}
