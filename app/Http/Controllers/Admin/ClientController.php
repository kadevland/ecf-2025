<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Client\ClientIndexViewPage;
use App\Application\UseCases\User\AfficherClients\AfficherClientsMapper;
use App\Application\UseCases\User\AfficherClients\AfficherClientsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\ClientSearchRequest;
use Illuminate\Http\Response;

/**
 * Contrôleur pour la gestion des clients
 */
final class ClientController extends Controller
{
    public function __construct(
        private readonly AfficherClientsUseCase $afficherClientsUseCase,
        private readonly AfficherClientsMapper $mapper,
    ) {}

    /**
     * Affiche la liste des clients
     */
    public function index(ClientSearchRequest $request): Response
    {
        $useCaseRequest = $this->mapper->mapToRequest($request);
        $response       = $this->afficherClientsUseCase->execute($useCaseRequest);

        $viewPage = ClientIndexViewPage::creer($response, $request);

        return response()->view('admin.clients.index', compact('viewPage'));
    }
}
