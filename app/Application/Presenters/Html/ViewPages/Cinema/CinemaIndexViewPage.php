<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Cinema;

use App\Application\Presenters\Html\ViewPages\Cinema\Elements\CinemaSearchElementView;
use App\Application\Presenters\Html\ViewPages\Elements\Table\CinemaListElement;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Cinema\CinemaSearchRequest;
use Illuminate\Support\Collection;

final readonly class CinemaIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly CinemaSearchElementView $searchForm,
        public readonly CinemaListElement $cinemaList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Cinémas',
    ) {}

    /**
     * @return Collection<int, array{label: string, url: string|null}>
     */
    /**
     * Créer une CinemaIndexViewPage complète
     */
    public static function creer(AfficherCinemasResponse $response, CinemaSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            cinemaList: CinemaListElement::creer($response, $request),
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
    private static function createSearchForm(CinemaSearchRequest $request): CinemaSearchElementView
    {
        $safe = $request->safe();

        return new CinemaSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            operationnel: isset($safe->operationnel) ? (bool) $safe->operationnel : null,
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
            ['label' => 'Cinémas', 'url' => null],
        ]);
    }
}
