<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Film;

use App\Application\Presenters\Html\ViewPages\Elements\Table\FilmListElement;
use App\Application\Presenters\Html\ViewPages\Film\Elements\FilmSearchElementView;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Film\AfficherFilms\AfficherFilmsResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Film\FilmSearchRequest;
use Illuminate\Support\Collection;

final readonly class FilmIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly FilmSearchElementView $searchForm,
        public readonly FilmListElement $filmList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Films',
    ) {}

    /**
     * @return Collection<int, array{label: string, url: string|null}>
     */
    /**
     * Créer une FilmIndexViewPage complète
     */
    public static function creer(AfficherFilmsResponse $response, FilmSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            filmList: FilmListElement::creer($response, $request),
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
    private static function createSearchForm(FilmSearchRequest $request): FilmSearchElementView
    {
        $safe = $request->safe();

        return new FilmSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            categorie: isset($safe->categorie) ? (string) $safe->categorie : null,
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
            ['label' => 'Films', 'url' => null],
        ]);
    }
}
