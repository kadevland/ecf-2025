<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Salle;

use App\Application\Presenters\Html\ViewPages\Elements\Table\SalleListElement;
use App\Application\Presenters\Html\ViewPages\Salle\Elements\SalleSearchElementView;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Salle\AfficherSalles\AfficherSallesResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Salle\SalleSearchRequest;
use App\Models\Cinema;
use Illuminate\Support\Collection;

final readonly class SalleIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly SalleSearchElementView $searchForm,
        public readonly SalleListElement $salleList,
        public readonly Collection $breadcrumbs,
        public readonly string $cinema,
        public readonly string $title = 'Gestion des Salles',

    ) {}

    /**
     * @return Collection<int, array{label: string, url: string|null}>
     */
    /**
     * Créer une SalleIndexViewPage complète
     */
    public static function creer(AfficherSallesResponse $response, SalleSearchRequest $request, Cinema $cinema): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            salleList: SalleListElement::creer($response, $request),
            breadcrumbs: self::createBreadcrumbs($cinema),
            cinema: $cinema->nom
        );
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * Créer le formulaire de recherche
     */
    private static function createSearchForm(SalleSearchRequest $request): SalleSearchElementView
    {
        $safe = $request->safe();

        return new SalleSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            cinema_id: isset($safe->cinema_id) ? (string) $safe->cinema_id : null,
            etat: isset($safe->etat) ? (string) $safe->etat : null,
            perPage: (int) ($safe->perPage ?? 15),
        );
    }

    /**
     * Créer les breadcrumbs
     */
    private static function createBreadcrumbs(Cinema $cinema): Collection
    {
        return collect([
            ['label' => 'Dashboard', 'href' => Navigation::gestion()->dashboard()],
            ['label' => 'Cinemas :'.$cinema->nom, 'href' => Navigation::gestion()->supervision()->cinemas()],
            ['label' => 'Salles', 'href' => null],
        ]);
    }
}
