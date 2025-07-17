<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Billet;

use App\Application\Presenters\Html\ViewPages\Billet\Elements\BilletSearchElementView;
use App\Application\Presenters\Html\ViewPages\Elements\Table\BilletListElement;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Application\UseCases\Billet\AfficherBillets\AfficherBilletsResponse;
use App\Common\Navigation;
use App\Http\Requests\Admin\Billet\BilletSearchRequest;
use Illuminate\Support\Collection;

final readonly class BilletIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly BilletSearchElementView $searchForm,
        public readonly BilletListElement $billetList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Billets',
    ) {}

    /**
     * Créer une BilletIndexViewPage complète
     */
    public static function creer(AfficherBilletsResponse $response, BilletSearchRequest $request): self
    {
        return new self(
            searchForm: self::createSearchForm($request),
            billetList: BilletListElement::creer($response, $request),
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
    private static function createSearchForm(BilletSearchRequest $request): BilletSearchElementView
    {
        $safe = $request->safe();

        return new BilletSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            typeTarif: (string) ($safe->typeTarif ?? ''),
            utilise: $safe->utilise ?? null,
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
            ['label' => 'Billets', 'url' => null],
        ]);
    }
}
