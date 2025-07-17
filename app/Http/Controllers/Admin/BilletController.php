<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Billet\BilletIndexViewPage;
use App\Application\UseCases\Billet\AfficherBillets\AfficherBilletsMapper;
use App\Application\UseCases\Billet\AfficherBillets\AfficherBilletsResponse;
use App\Application\UseCases\Billet\AfficherBillets\AfficherBilletsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Billet\BilletSearchRequest;
use Illuminate\Contracts\View\View;

/**
 * Contrôleur pour la gestion des billets
 */
final class BilletController extends Controller
{
    public function __construct(
        private readonly AfficherBilletsUseCase $afficherBilletsUseCase,
        private readonly AfficherBilletsMapper $mapper,
    ) {}

    /**
     * Affiche la liste des billets
     */
    public function __invoke(BilletSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor
        $requestDto = $this->mapper->mapToRequest($request->validated());

        // Exécuter le use case
        /**
         * @var AfficherBilletsResponse $response
         */
        $response = $this->afficherBilletsUseCase->execute($requestDto);

        return view('admin.billets.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherBilletsResponse $response, BilletSearchRequest $request): BilletIndexViewPage
    {
        return BilletIndexViewPage::creer($response, $request);
    }
}
