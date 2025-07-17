<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Application\Presenters\Html\ViewModels\MonCompteViewModel;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class MonCompteController extends Controller
{
    /**
     * Afficher la page Mon Compte
     */
    public function __invoke(Request $request): View
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer le profil client de l'utilisateur connecté
        $client = $user->client;

        // Compter directement les réservations du client connecté
        $nombreReservations = DB::table('reservations')
            ->where('user_id', $user->id)
            ->count();

        // Compter directement les films vus (réservations avec statut 'terminee')
        $nombreFilmsVus = DB::table('reservations')
            ->join('seances', 'reservations.seance_id', '=', 'seances.id')
            ->where('reservations.user_id', $user->id)
            ->where('reservations.statut', 'terminee')
            ->distinct('seances.film_id')
            ->count('seances.film_id');

        $viewModel = new MonCompteViewModel($user, $nombreReservations, $nombreFilmsVus);

        return view('client.mon-compte.index', [
            'viewModel'   => $viewModel,
            'breadcrumbs' => $viewModel->breadcrumbs(),
        ]);
    }
}
