<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\ViewModels\SeanceViewModel;
use App\Domain\Entities\Seance\Seance;

final readonly class SeanceItemListElement extends ItemListElement
{
    public function __construct(
        public readonly SeanceViewModel $seance,
        ActionListView $actions,
    ) {
        parent::__construct($actions);
    }

    /**
     * Créer un item séance avec ses actions
     */
    public static function creer(Seance $seance): self
    {
        $seanceViewModel = new SeanceViewModel($seance);

        // Créer les actions pour cette séance
        $actions = collect([
            new Action(
                label: 'Voir',
                url: '#', // route('gestion.supervision.seances.show', $seanceViewModel->id()),
                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                class: 'btn-info',
            ),
            new Action(
                label: 'Réservations',
                url: '#', // TODO: créer la route reservations
                icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                class: 'btn-secondary',
                tooltip: 'Voir les réservations de cette séance',
            ),
            new Action(
                label: 'Modifier',
                url: '#', // route('gestion.supervision.seances.edit', $seanceViewModel->id()),
                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                class: 'btn-warning',
                enabled: $seanceViewModel->peutEtreModifiee(),
            ),
            new Action(
                label: 'Annuler',
                url: '#', // TODO: créer la route cancel
                icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                class: 'btn-warning',
                enabled: $seanceViewModel->peutEtreAnnulee(),
                confirmMessage: 'Êtes-vous sûr de vouloir annuler cette séance ?',
            ),
            new Action(
                label: 'Supprimer',
                url: '#', // route('gestion.supervision.seances.destroy', $seanceViewModel->id()),
                icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                class: 'btn-error',
                enabled: $seanceViewModel->peutEtreSupprimee(),
                confirmMessage: 'Êtes-vous sûr de vouloir supprimer cette séance ?',
            ),
        ]);

        $actionList = new ActionListView(
            actions: $actions,
            isDropdown: true,
            dropdownLabel: 'Actions',
        );

        return new self($seanceViewModel, $actionList);
    }

    /**
     * Affiche la valeur pour une colonne donnée
     */
    public function displayValue(string $columnKey): mixed
    {
        return match ($columnKey) {
            'date'    => $this->seance->dateComplete(),
            'film'    => $this->seance->titreFilm(),
            'salle'   => $this->seance->nomSalle(),
            'etat'    => $this->seance->etat(),
            'qualite' => $this->seance->qualiteProjection(),
            'prix'    => $this->seance->tarif(),
            'places'  => sprintf('%d/%d', $this->seance->placesDisponibles(), $this->seance->capaciteTotale()),
            default   => property_exists($this->seance, $columnKey) ? $this->seance->{$columnKey} : '',
        };
    }

    /**
     * Obtient les badges pour cette séance
     */
    public function getBadges(): array
    {
        return $this->seance->badges();
    }
}
