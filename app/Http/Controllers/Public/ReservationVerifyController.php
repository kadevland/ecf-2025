<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\View\View;

final class ReservationVerifyController extends Controller
{
    public function __invoke(Reservation $reservation): View
    {
        // Charger les relations nécessaires
        $reservation->load([
            'seance.film',
            'seance.salle.cinema',
        ]);

        return view('public.reservation-verify-simple', [
            'reservation' => $reservation,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('accueil')],
                ['label' => 'Vérification Réservation', 'url' => null],
            ],
        ]);
    }
}
