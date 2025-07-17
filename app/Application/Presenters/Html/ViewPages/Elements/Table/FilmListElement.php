<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\Film\AfficherFilms\AfficherFilmsResponse;
use App\Http\Requests\Admin\Film\FilmSearchRequest;
use Illuminate\Support\Collection;

final readonly class FilmListElement extends ListElement
{
    /**
     * Créer un FilmListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherFilmsResponse $response, FilmSearchRequest $request): self
    {

        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouveau film',
                url: '#',// route('gestion.supervision.films.create'),
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Films',
            pagination: $response->pagination ? new PaginationLinkRenderer($response->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(FilmSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Titre', 'titre', true, $currentSort, $currentDirection),
            new HeaderCell('Catégorie', 'categorie', true, $currentSort, $currentDirection),
            new HeaderCell('Réalisateur', 'realisateur'),
            new HeaderCell('Durée', 'duree_minutes', true, $currentSort, $currentDirection),
            new HeaderCell('Date sortie', 'date_sortie', true, $currentSort, $currentDirection),
            new HeaderCell('Note', 'note_moyenne', true, $currentSort, $currentDirection),
            new HeaderCell('Créé le', 'created_at', true, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, FilmItemListElement>
     */
    private static function creerItemList(AfficherFilmsResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->films->map(fn ($film) => FilmItemListElement::creer($film)));
    }
}
