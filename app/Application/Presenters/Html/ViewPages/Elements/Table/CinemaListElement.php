<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasResponse;
use App\Http\Requests\Admin\Cinema\CinemaSearchRequest;
use Illuminate\Support\Collection;

final readonly class CinemaListElement extends ListElement
{
    /**
     * Créer un CinemaListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherCinemasResponse $response, CinemaSearchRequest $request): self
    {

        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouveau cinéma',
                url: '#',// route('gestion.supervision.cinemas.create'),
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Cinémas',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(CinemaSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Nom', 'nom', true, $currentSort, $currentDirection),
            new HeaderCell('Ville', 'ville', true, $currentSort, $currentDirection),
            new HeaderCell('Pays', 'pays'),
            new HeaderCell('Statut', 'status', true, $currentSort, $currentDirection),
            new HeaderCell('Salles', 'nombreSalles', false, $currentSort, $currentDirection),
            new HeaderCell('Code', 'code_cinema', false, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, CinemaItemListElement>
     */
    private static function creerItemList(AfficherCinemasResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->cinemas->map(fn ($cinema) => CinemaItemListElement::creer($cinema)));
    }
}
