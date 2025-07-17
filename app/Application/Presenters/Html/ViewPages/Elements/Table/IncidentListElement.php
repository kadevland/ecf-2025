<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsResponse;
use App\Http\Requests\Admin\Incident\IncidentSearchRequest;
use Illuminate\Support\Collection;

final readonly class IncidentListElement extends ListElement
{
    /**
     * Créer un IncidentListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherIncidentsResponse $response, IncidentSearchRequest $request): self
    {
        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouvel incident',
                url: '#',
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Incidents',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(IncidentSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Titre', 'titre', true, $currentSort, $currentDirection),
            new HeaderCell('Créé le', 'created_at', true, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, IncidentItemListElement>
     */
    private static function creerItemList(AfficherIncidentsResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->incidents->map(fn ($incident) => IncidentItemListElement::creer($incident)));
    }
}
