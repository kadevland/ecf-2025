<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Film;
use App\Models\Seance;
use Carbon\CarbonImmutable;

final class AccueilController extends Controller
{
    public function __invoke(): \Illuminate\Contracts\View\View
    {

        // Films à l'affiche (films avec séances dans les 7 prochains jours)
        $dateDebut = CarbonImmutable::today();
        $dateFin   = CarbonImmutable::today()->addDays(7);

        $filmsALAffiche = Film::whereHas('seances', function ($query) use ($dateDebut, $dateFin) {
            $query->whereBetween('date_heure_debut', [$dateDebut, $dateFin])
                ->where('etat', 'programmee');
        })
            ->orderBy('note_moyenne', 'desc')
            ->limit(6)
            ->get()
            ->map(function (Film $film) {
                return [
                    'model'         => $film,
                    'id'            => $film->id,
                    'titre'         => $film->titre,
                    'categorie'     => $film->categorie?->value ?? 'Non classé',
                    'duree'         => $film->getDureeFormatee(),
                    'affiche'       => $film->affiche_url ?? 'https://via.placeholder.com/300x450',
                    'note'          => $film->note_moyenne,
                    'note_formatee' => $film->getNoteMoyenneFormatee(),
                ];
            });

        // Prochaines séances (aujourd'hui et demain)
        $now    = CarbonImmutable::now()->subMinutes(30);
        $demain = CarbonImmutable::tomorrow();

        $prochainesSeances = Seance::whereBetween('date_heure_debut', [$now, $demain])
            ->where('etat', 'programmee')
            ->where('places_disponibles', '>', 0)
            ->with(['film', 'salle.cinema'])
            ->orderBy('date_heure_debut')
            ->limit(6)
            ->get()
            ->map(function (Seance $seance) {
                return [
                    'film'               => $seance->film->titre,
                    'film_model'         => $seance->film,
                    'cinema'             => $seance->salle->cinema->nom,
                    'cinema_model'       => $seance->salle->cinema,
                    'heure'              => CarbonImmutable::parse($seance->date_heure_debut)->format('H:i'),
                    'date'               => CarbonImmutable::parse($seance->date_heure_debut)->locale('fr')
                        ->isoFormat('ddd D MMM'),
                    'salle'              => $seance->salle->nom,
                    'version'            => $seance->version,
                    'qualite'            => $seance->qualite_projection ?? 'Standard',
                    'places_disponibles' => $seance->places_disponibles,
                    'prix'               => number_format($seance->prix_base, 2, ',', ' ').' €',
                ];
            });

        // Statistiques globales
        $statistiques = [
            'films'           => Film::count(),
            'cinemas'         => Cinema::where('statut', 'actif')->count(),
            'seances_semaine' => Seance::whereBetween('date_heure_debut', [$dateDebut, $dateFin])
                ->where('etat', 'programmee')
                ->count(),
            'salles'          => Cinema::where('statut', 'actif')->withCount('salles')
                ->get()
                ->sum('salles_count'),
        ];

        // Cinémas populaires
        $cinemasPopulaires = Cinema::where('statut', 'actif')
            ->orderBy('nom')
            ->limit(3)
            ->get()
            ->map(function (Cinema $cinema) {
                return [
                    'model'         => $cinema,
                    'nom'           => $cinema->nom,
                    'ville'         => $cinema->adresse['ville'] ?? '',
                    'nombre_salles' => $cinema->salles()
                        ->count(),
                    'photo'         => 'https://picsum.photos/400/250?random='.$cinema->id,
                ];
            });

        return view('app.accueil.index', [
            'filmsALAffiche'    => $filmsALAffiche,
            'prochainesSeances' => $prochainesSeances,
            'statistiques'      => $statistiques,
            'cinemasPopulaires' => $cinemasPopulaires,
        ]);
    }
}
