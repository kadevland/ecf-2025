<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\DTOs\Reservation\AfficherReservationsResponse;
use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Http\Requests\Admin\Reservation\ReservationSearchRequest;
use Illuminate\Support\Collection;

final readonly class ReservationListElement extends ListElement
{
    /**
     * Créer un ReservationListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherReservationsResponse $response, ReservationSearchRequest $request): self
    {
        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouvelle réservation',
                url: '#', // TODO: route pour créer une réservation
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Réservations',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(ReservationSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('N° Réservation', 'numero_reservation', true, $currentSort, $currentDirection),
            new HeaderCell('Statut', 'statut', true, $currentSort, $currentDirection),
            new HeaderCell('Places', 'nombre_places'),
            new HeaderCell('Prix Total', 'prix_total', true, $currentSort, $currentDirection),
            new HeaderCell('Cinéma', 'code_cinema', true, $currentSort, $currentDirection),
            new HeaderCell('Créé le', 'created_at', true, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, ReservationItemListElement>
     */
    private static function creerItemList(AfficherReservationsResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->reservations->map(fn ($reservation) => ReservationItemListElement::creer($reservation)));
    }
}
