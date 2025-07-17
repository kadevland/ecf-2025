<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Incident\IncidentIndexViewPage;
use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsMapper;
use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsResponse;
use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Incident\IncidentSearchRequest;
use Illuminate\Contracts\View\View;

/**
 * Contrôleur pour la gestion des incidents
 */
final class IncidentController extends Controller
{
    public function __construct(
        private readonly AfficherIncidentsUseCase $afficherIncidentsUseCase,
        private readonly AfficherIncidentsMapper $mapper,
    ) {}

    /**
     * Affiche la liste des incidents
     */
    public function __invoke(IncidentSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor
        $requestDto = $this->mapper->mapToRequest($request->validated());

        // Exécuter le use case
        /**
         * @var AfficherIncidentsResponse $response
         */
        $response = $this->afficherIncidentsUseCase->execute($requestDto);

        return view('admin.incidents.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherIncidentsResponse $response, IncidentSearchRequest $request): IncidentIndexViewPage
    {
        return IncidentIndexViewPage::creer($response, $request);
    }
}
