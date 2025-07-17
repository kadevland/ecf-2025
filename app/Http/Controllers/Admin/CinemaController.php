<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Cinema\CinemaIndexViewPage;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasMapper;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasResponse;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cinema\CinemaSearchRequest;
use Illuminate\Contracts\View\View;

final class CinemaController extends Controller
{
    public function __construct(
        private readonly AfficherCinemasUseCase $afficherCinemasUseCase,
        private readonly AfficherCinemasMapper $mapper
    ) {}

    public function __invoke(CinemaSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor
        $requestDto = $this->mapper->mapToRequest($request->validated());

        // Exécuter le use case
        /**
         * @var AfficherCinemasResponse $response
         */
        $response = $this->afficherCinemasUseCase->execute($requestDto);

        return view('admin.cinemas.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherCinemasResponse $response, CinemaSearchRequest $request): CinemaIndexViewPage
    {
        return CinemaIndexViewPage::creer($response, $request);

    }
}
