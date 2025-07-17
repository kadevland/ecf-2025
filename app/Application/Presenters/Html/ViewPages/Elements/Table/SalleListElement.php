<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\Salle\AfficherSalles\AfficherSallesResponse;
use App\Http\Requests\Admin\Salle\SalleSearchRequest;
use Illuminate\Support\Collection;

final readonly class SalleListElement extends ListElement
{
    /**
     * Créer un SalleListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherSallesResponse $response, SalleSearchRequest $request): self
    {

        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouvelle salle',
                url: '#',// route('gestion.supervision.salles.create'),
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Salles',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(SalleSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Numéro', 'numero', true, $currentSort, $currentDirection),
            new HeaderCell('Nom', 'nom', true, $currentSort, $currentDirection),
            new HeaderCell('Capacité', 'capacite', true, $currentSort, $currentDirection),
            new HeaderCell('État', 'etat', true, $currentSort, $currentDirection),
            new HeaderCell('Qualité', 'qualite_projection'),
            new HeaderCell('Cinéma', 'cinema'),
            // new HeaderCell('Créé le', 'created_at', true, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, SalleItemListElement>
     */
    private static function creerItemList(AfficherSallesResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->salles->map(fn ($salle) => SalleItemListElement::creer($salle)));
    }
}
