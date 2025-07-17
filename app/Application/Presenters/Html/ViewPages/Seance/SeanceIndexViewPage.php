<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Seance;

use App\Application\Presenters\Html\ViewPages\Elements\Table\SeanceListElement;
use App\Application\Presenters\Html\ViewPages\Seance\Elements\SeanceSearchElementView;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Seance\AfficherSeances\AfficherSeancesResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Seance\SeanceSearchRequest;
use Illuminate\Support\Collection;

final readonly class SeanceIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly SeanceSearchElementView $searchForm,
        public readonly SeanceListElement $seanceList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Séances',
    ) {}

    /**
     * Créer une SeanceIndexViewPage complète
     */
    public static function creer(AfficherSeancesResponse $response, SeanceSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            seanceList: SeanceListElement::creer($response, $request),
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
    private static function createSearchForm(SeanceSearchRequest $request): SeanceSearchElementView
    {
        $safe = $request->safe();

        return new SeanceSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            etat: isset($safe->etat) ? (string) $safe->etat : null,
            qualiteProjection: isset($safe->qualite_projection) ? (string) $safe->qualite_projection : null,
            filmId: isset($safe->film_id) ? (int) $safe->film_id : null,
            salleId: isset($safe->salle_id) ? (int) $safe->salle_id : null,
            dateDebut: isset($safe->date_debut) ? (string) $safe->date_debut : null,
            dateFin: isset($safe->date_fin) ? (string) $safe->date_fin : null,
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
            ['label' => 'Séances', 'url' => null],
        ]);
    }
}
