<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Seance;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;

final class FilmDetailController extends Controller
{
    public function __invoke(Film $film): View
    {
        // Récupérer les séances du film dans les 14 prochains jours
        $dateDebut = CarbonImmutable::today();
        $dateFin   = CarbonImmutable::today()->addDays(14);

        $seances = Seance::where('film_id', $film->id)
            ->where('date_heure_debut', '>', CarbonImmutable::now())
            ->where('date_heure_debut', '<=', CarbonImmutable::now()->addDays(14))
            ->where('etat', \App\Domain\Enums\EtatSeance::Programmee)
            ->with(['salle.cinema'])
            ->orderBy('date_heure_debut')
            ->get();

        // Grouper les séances par cinéma puis par jour
        $seancesParCinema = $seances->groupBy(function ($seance) {
            return $seance->salle->cinema->id;
        })->map(function ($seancesCinema) {
            $cinema = $seancesCinema->first()->salle->cinema;

            // Grouper par jour
            $seancesParJour = $seancesCinema->groupBy(function ($seance) {
                return CarbonImmutable::parse($seance->date_heure_debut)->format('Y-m-d');
            })->map(function ($seancesJour, $date) {
                return [
                    'date'           => CarbonImmutable::parse($date),
                    'date_formatted' => CarbonImmutable::parse($date)->locale('fr')->isoFormat('dddd D MMMM'),
                    'is_today'       => CarbonImmutable::parse($date)->isToday(),
                    'is_tomorrow'    => CarbonImmutable::parse($date)->isTomorrow(),
                    'seances'        => $seancesJour->map(function ($seance) {
                        return [
                            'id'                 => $seance->id,
                            'heure'              => $seance->getHeureDebut(),
                            'salle'              => $seance->salle->nom,
                            'version'            => $seance->getVersionComplete(),
                            'qualite'            => $seance->qualite_projection?->value ?? 'standard',
                            'places_disponibles' => $seance->places_disponibles,
                            'prix'               => $seance->getPrixFormate(),
                            'complet'            => $seance->isFull(),
                            'peu_de_places'      => $seance->places_disponibles < 10,
                        ];
                    })->sortBy('heure')->values(),
                ];
            })->sortKeys()->values();

            return [
                'cinema' => [
                    'id'               => $cinema->id,
                    'nom'              => $cinema->nom,
                    'ville'            => $cinema->adresse['ville'] ?? '',
                    'adresse_complete' => $cinema->getAdresseComplete(),
                ],
                'seances_par_jour' => $seancesParJour,
            ];
        })->values();

        // Informations additionnelles du film
        $filmData = [
            'id'                   => $film->id,
            'titre'                => $film->titre,
            'description'          => $film->description,
            'duree'                => $film->getDureeFormatee(),
            'duree_minutes'        => $film->duree_minutes,
            'categorie'            => $film->categorie?->value ?? 'Non classé',
            'realisateur'          => $film->realisateur,
            'acteurs'              => $film->acteurs ?? [],
            'pays_origine'         => $film->pays_origine,
            'date_sortie'          => $film->date_sortie,
            'date_sortie_formatee' => $film->date_sortie?->locale('fr')->isoFormat('D MMMM YYYY'),
            'affiche'              => $film->getPosterUrl(),
            'bande_annonce'        => $film->bande_annonce_url,
            'note_moyenne'         => $film->note_moyenne,
            'note_formatee'        => $film->getNoteMoyenneFormatee(),
            'nombre_votes'         => $film->nombre_votes,
            'has_trailer'          => $film->hasTrailer(),
            'from_tmdb'            => $film->isFromTMDB(),
        ];

        return view('app.films.show', [
            'film'             => $filmData,
            'seancesParCinema' => $seancesParCinema,
            'nombreSeances'    => $seances->count(),
            'nombreCinemas'    => $seancesParCinema->count(),
        ]);
    }
}
