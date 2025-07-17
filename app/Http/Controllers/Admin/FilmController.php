<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Presenters\Html\ViewPages\Film\FilmIndexViewPage;
use App\Application\UseCases\Film\AfficherFilms\AfficherFilmsMapper;
use App\Application\UseCases\Film\AfficherFilms\AfficherFilmsResponse;
use App\Application\UseCases\Film\AfficherFilms\AfficherFilmsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Film\FilmSearchRequest;
use Illuminate\Contracts\View\View;

final class FilmController extends Controller
{
    public function __construct(
        private readonly AfficherFilmsUseCase $afficherFilmsUseCase,
        private readonly AfficherFilmsMapper $mapper
    ) {}

    public function __invoke(FilmSearchRequest $request): View
    {
        // Convertir la requête HTTP validée en DTO avec Valinor
        $requestDto = $this->mapper->mapToRequest($request->validated());

        // Exécuter le use case
        /**
         * @var AfficherFilmsResponse $response
         */
        $response = $this->afficherFilmsUseCase->execute($requestDto);

        return view('admin.films.index', ['viewPage' => $this->presenterHtml($response, $request)]);
    }

    protected function presenterHtml(AfficherFilmsResponse $response, FilmSearchRequest $request): FilmIndexViewPage
    {
        return FilmIndexViewPage::creer($response, $request);

    }
}
