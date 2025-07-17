<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Client;

use App\Application\Presenters\Html\ViewPages\Client\Elements\ClientSearchElementView;
use App\Application\Presenters\Html\ViewPages\Elements\Table\ClientListElement;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\User\AfficherClients\AfficherClientsResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Client\ClientSearchRequest;
use Illuminate\Support\Collection;

final readonly class ClientIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly ClientSearchElementView $searchForm,
        public readonly ClientListElement $clientList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Clients',
    ) {}

    /**
     * Créer une ClientIndexViewPage complète
     */
    public static function creer(AfficherClientsResponse $response, ClientSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            clientList: ClientListElement::creer($response, $request),
            breadcrumbs: self::createBreadcrumbs(),
        );
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * Créer le formulaire de recherche
     */
    private static function createSearchForm(ClientSearchRequest $request): ClientSearchElementView
    {
        $safe = $request->safe();

        return new ClientSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            perPage: (int) ($safe->perPage ?? 15),
        );
    }

    /**
     * Créer les breadcrumbs
     */
    private static function createBreadcrumbs(): Collection
    {
        return collect([
            ['label' => 'Dashboard', 'url' => Navigation::gestion()->dashboard()],
            ['label' => 'Clients', 'url' => null],
        ]);
    }
}
