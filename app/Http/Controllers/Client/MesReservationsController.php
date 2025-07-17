<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class MesReservationsController extends Controller
{
    /**
     * Afficher la page Mes Réservations
     */
    public function __invoke(Request $request): View
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer les réservations du client connecté avec les relations, filtres et pagination
        $query = Reservation::where('user_id', $user->id)
            ->with(['seance.film', 'seance.salle.cinema', 'billets']);

        // Filtrer par statut si demandé
        if ($request->has('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }

        // Trier selon le paramètre
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'ancien':
                $query->orderBy('created_at', 'asc');
                break;
            case 'seance':
                $query->join('seances', 'reservations.seance_id', '=', 'seances.id')
                    ->orderBy('seances.date_heure_debut', 'desc')
                    ->select('reservations.*');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('client.mes-reservations.index', [
            'user'         => $user,
            'reservations' => $reservations,
            'breadcrumbs'  => [
                ['label' => 'Accueil', 'url' => route('accueil')],
                ['label' => 'Mes Réservations', 'url' => null],
            ],
        ]);
    }
}
