<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\ViewModels\SalleViewModel;
use App\Domain\Entities\Salle\Salle;

final readonly class SalleItemListElement extends ItemListElement
{
    public function __construct(
        public readonly SalleViewModel $salle,
        ActionListView $actions,
    ) {
        parent::__construct($actions);
    }

    /**
     * Créer un item salle avec ses actions
     */
    public static function creer(Salle $salle): self
    {
        $salleViewModel = new SalleViewModel($salle);

        // Créer les actions pour cette salle
        $actions = collect([
            new Action(
                label: 'Voir',
                url: '#', // TODO: créer la route show
                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                class: 'btn-info',
            ),
            new Action(
                label: 'Séances',
                url: '#', // TODO: créer la route seances
                icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                class: 'btn-secondary',
                tooltip: 'Voir les séances de cette salle',
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
                confirmMessage: 'Êtes-vous sûr de vouloir supprimer cette salle ?',
            ),
        ]);

        $actionList = new ActionListView(
            actions: $actions,
            isDropdown: true,
            dropdownLabel: 'Actions',
        );

        return new self($salleViewModel, $actionList);
    }

    /**
     * Affiche la valeur pour une colonne donnée
     */
    public function displayValue(string $columnKey): mixed
    {
        return match ($columnKey) {
            'numero'             => $this->salle->numero(),
            'nom'                => $this->salle->nom(),
            'capacite'           => $this->salle->capacite(),
            'etat'               => $this->salle->etat(),
            'qualite_projection' => $this->salle->qualitesProjection(),
            'cinema'             => $this->salle->nomCinema(),
            'created_at'         => '',
            default              => '',
        };
    }
}
