<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Seance\SeanceIndexViewPage;
use App\Application\UseCases\Seance\AfficherSeances\AfficherSeancesMapper;
use App\Application\UseCases\Seance\AfficherSeances\AfficherSeancesResponse;
use App\Application\UseCases\Seance\AfficherSeances\AfficherSeancesUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Seance\SeanceSearchRequest;
use Illuminate\Contracts\View\View;

final class SeanceController extends Controller
{
    public function __construct(
        private readonly AfficherSeancesUseCase $afficherSeancesUseCase,
        private readonly AfficherSeancesMapper $mapper
    ) {}

    public function __invoke(SeanceSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO
        $requestDto = $this->mapper->mapToRequest($request);

        // Exécuter le use case
        /** @var AfficherSeancesResponse $response */
        $response = $this->afficherSeancesUseCase->execute($requestDto);

        return view('admin.seances.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherSeancesResponse $response, SeanceSearchRequest $request): SeanceIndexViewPage
    {
        return SeanceIndexViewPage::creer($response, $request);
    }
}
