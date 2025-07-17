<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Salle\SalleIndexViewPage;
use App\Application\UseCases\Salle\AfficherSalles\AfficherSallesMapper;
use App\Application\UseCases\Salle\AfficherSalles\AfficherSallesResponse;
use App\Application\UseCases\Salle\AfficherSalles\AfficherSallesUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Salle\SalleSearchRequest;
use App\Models\Cinema as CinemaModel;
use Illuminate\Contracts\View\View;

final class SalleController extends Controller
{
    public function __construct(
        private readonly AfficherSallesUseCase $afficherSallesUseCase,
        private readonly AfficherSallesMapper $mapper
    ) {}

    public function __invoke(SalleSearchRequest $request, CinemaModel $cinema): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor

        $requestDto = $this->mapper->mapToRequest(array_merge($request->validated(), ['cinema_id' => $cinema->id, 'cinema_uuid' => $cinema->uuid]));

        // Exécuter le use case
        /**
         * @var AfficherSallesResponse $response
         */
        $response = $this->afficherSallesUseCase->execute($requestDto);

        return view('admin.salles.index', ['viewPage' => $this->presenterHtml($response, $request, $cinema)]);
    }

    protected function presenterHtml(AfficherSallesResponse $response, SalleSearchRequest $request, CinemaModel $cinema): SalleIndexViewPage
    {
        return SalleIndexViewPage::creer($response, $request, $cinema);
    }
}
