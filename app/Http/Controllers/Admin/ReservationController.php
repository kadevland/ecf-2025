<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\DTOs\Reservation\AfficherReservationsResponse;
use App\Application\Presenters\Html\ViewPages\Reservation\ReservationIndexViewPage;
use App\Application\UseCases\Reservation\AfficherReservationsMapper;
use App\Application\UseCases\Reservation\AfficherReservationsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Reservation\ReservationSearchRequest;
use Illuminate\Contracts\View\View;

final class ReservationController extends Controller
{
    public function __construct(
        private readonly AfficherReservationsUseCase $afficherReservationsUseCase,
        private readonly AfficherReservationsMapper $mapper
    ) {}

    public function __invoke(ReservationSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor
        $requestDto = $this->mapper->mapToRequest($request->validated());

        // Exécuter le use case
        /**
         * @var AfficherReservationsResponse $response
         */
        $response = $this->afficherReservationsUseCase->execute($requestDto);

        return view('admin.reservations.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherReservationsResponse $response, ReservationSearchRequest $request): ReservationIndexViewPage
    {
        return ReservationIndexViewPage::creer($response, $request);
    }
}
