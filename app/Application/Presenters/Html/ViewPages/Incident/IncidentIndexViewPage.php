<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Incident;

use App\Application\Presenters\Html\ViewPages\Elements\Table\IncidentListElement;
use App\Application\Presenters\Html\ViewPages\Incident\Elements\IncidentSearchElementView;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Incident\IncidentSearchRequest;
use Illuminate\Support\Collection;

final readonly class IncidentIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly IncidentSearchElementView $searchForm,
        public readonly IncidentListElement $incidentList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Incidents',
    ) {}

    /**
     * Créer une IncidentIndexViewPage complète
     */
    public static function creer(AfficherIncidentsResponse $response, IncidentSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            incidentList: IncidentListElement::creer($response, $request),
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
    private static function createSearchForm(IncidentSearchRequest $request): IncidentSearchElementView
    {
        $safe = $request->safe();

        return new IncidentSearchElementView(
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
            ['label' => 'Incidents', 'url' => null],
        ]);
    }
}
