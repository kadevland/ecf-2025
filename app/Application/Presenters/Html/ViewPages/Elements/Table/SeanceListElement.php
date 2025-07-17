<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\Seance\AfficherSeances\AfficherSeancesResponse;
use App\Http\Requests\Admin\Seance\SeanceSearchRequest;
use Illuminate\Support\Collection;

final readonly class SeanceListElement extends ListElement
{
    /**
     * Créer un SeanceListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherSeancesResponse $response, SeanceSearchRequest $request): self
    {
        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouvelle séance',
                url: '#', // route('gestion.supervision.seances.create'),
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Séances',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(SeanceSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Date/Heure', 'date', true, $currentSort, $currentDirection),
            new HeaderCell('Film', 'film', false),
            new HeaderCell('Salle', 'salle', false),
            new HeaderCell('État', 'etat', true, $currentSort, $currentDirection),
            new HeaderCell('Qualité', 'qualite', true, $currentSort, $currentDirection),
            new HeaderCell('Prix', 'prix', true, $currentSort, $currentDirection),
            new HeaderCell('Places', 'places', false),
        ]);
    }

    /**
     * @return Collection<int, SeanceItemListElement>
     */
    private static function creerItemList(AfficherSeancesResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->seances)->map(fn ($seance) => SeanceItemListElement::creer($seance));
    }
}
