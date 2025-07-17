<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Reservation;

use App\Application\DTOs\Reservation\AfficherReservationsResponse;
use App\Application\Presenters\Html\ViewPages\Elements\ReservationSearchElementView;
use App\Application\Presenters\Html\ViewPages\Elements\Table\ReservationListElement;
use App\Application\Presenters\Html\ViewPages\ViewPage;
use App\Common\Navigation;
use App\Http\Requests\Admin\Reservation\ReservationSearchRequest;
use Illuminate\Support\Collection;

final readonly class ReservationIndexViewPage extends ViewPage
{
    public function __construct(
        public readonly ReservationSearchElementView $searchForm,
        public readonly ReservationListElement $reservationList,
        public readonly Collection $breadcrumbs,
        public readonly string $title = 'Gestion des Réservations',
    ) {}

    public static function creer(
        AfficherReservationsResponse $response,
        ReservationSearchRequest $request
    ): self {
        return new self(
            searchForm: self::createSearchForm($request),
            reservationList: ReservationListElement::creer($response, $request),
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
    private static function createSearchForm(ReservationSearchRequest $request): ReservationSearchElementView
    {
        $safe = $request->safe();

        return new ReservationSearchElementView(
            recherche: (string) ($safe->recherche ?? ''),
            statut: isset($safe->statut) ? $safe->statut : null,
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
            ['label' => 'Réservations', 'url' => null],
        ]);
    }
}
