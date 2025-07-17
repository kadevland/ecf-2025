<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\Pagination\PaginationLinkRenderer;
use App\Application\UseCases\User\AfficherClients\AfficherClientsResponse;
use App\Http\Requests\Admin\Client\ClientSearchRequest;
use Illuminate\Support\Collection;

final readonly class ClientListElement extends ListElement
{
    /**
     * Créer un ClientListElement à partir de la réponse du UseCase
     */
    public static function creer(AfficherClientsResponse $response, ClientSearchRequest $request): self
    {
        // Actions globales
        $globalActions = new ActionListView(collect([
            new Action(
                label: 'Nouveau client',
                url: '#',
                icon: 'M12 4v16m8-8H4',
                class: 'btn-primary',
            ),
        ]));

        return new self(
            headers: self::creerHeaders($request),
            items: self::creerItemList($response),
            actions: $globalActions,
            title: 'Clients',
            pagination: $response->clients->pagination ? new PaginationLinkRenderer($response->clients->pagination, $request) : null,
        );
    }

    /**
     * @return Collection<int, HeaderCell>
     */
    private static function creerHeaders(ClientSearchRequest $request): Collection
    {
        $safe             = $request->safe();
        $currentSort      = is_string($safe->sort ?? null) ? $safe->sort : null;
        $currentDirection = is_string($safe->direction ?? null) ? $safe->direction : null;

        // Créer les en-têtes de colonnes
        return collect([
            new HeaderCell('Email', 'email', true, $currentSort, $currentDirection),
            new HeaderCell('Nom', 'nom', false, $currentSort, $currentDirection),
            new HeaderCell('Prénom', 'prenom', false, $currentSort, $currentDirection),
            new HeaderCell('Statut', 'statut', true, $currentSort, $currentDirection),
            new HeaderCell('Créé le', 'created_at', false, $currentSort, $currentDirection),
        ]);
    }

    /**
     * @return Collection<int, ClientItemListElement>
     */
    private static function creerItemList(AfficherClientsResponse $response): Collection
    {
        // Créer les items de la liste
        return collect($response->clients->items->map(fn ($client) => ClientItemListElement::creer($client)));
    }
}
