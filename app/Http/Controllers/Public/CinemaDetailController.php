<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Seance;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;

final class CinemaDetailController extends Controller
{
    public function __invoke(Cinema $cinema): View
    {
        // Vérifier que le cinéma est actif
        if (! $cinema->isActif()) {
            abort(404);
        }

        // Charger les salles du cinéma
        $cinema->load('salles');

        // Récupérer les films actuellement à l'affiche dans ce cinéma
        // Logique : semaine civile, si vide alors semaine civile +1
        $now       = CarbonImmutable::now();
        $dateDebut = $now->startOfWeek(); // Lundi
        $dateFin   = $now->endOfWeek(); // Dimanche

        // Essayer la semaine civile actuelle
        $seances = Seance::whereHas('salle', function ($query) use ($cinema) {
            $query->where('cinema_id', $cinema->id);
        })
            ->where('date_heure_debut', '>', $now)
            ->where('date_heure_debut', '<=', $dateFin)
            ->where('etat', \App\Domain\Enums\EtatSeance::Programmee)
            ->with(['film', 'salle'])
            ->orderBy('date_heure_debut')
            ->get();

        // Si vide, prendre la semaine civile suivante
        if ($seances->isEmpty()) {
            $dateDebut = $now->copy()->startOfWeek()->addDays(7); // Lundi prochain
            $dateFin   = $now->copy()->endOfWeek()->addDays(7); // Dimanche prochain

            $seances = Seance::whereHas('salle', function ($query) use ($cinema) {
                $query->where('cinema_id', $cinema->id);
            })
                ->whereBetween('date_heure_debut', [$dateDebut, $dateFin])
                ->where('etat', \App\Domain\Enums\EtatSeance::Programmee)
                ->with(['film', 'salle'])
                ->orderBy('date_heure_debut')
                ->get();
        }

        // Si toujours vide, prendre les 10 prochaines séances
        if ($seances->isEmpty()) {
            $seances = Seance::whereHas('salle', function ($query) use ($cinema) {
                $query->where('cinema_id', $cinema->id);
            })
                ->where('date_heure_debut', '>', $now)
                ->where('etat', \App\Domain\Enums\EtatSeance::Programmee)
                ->with(['film', 'salle'])
                ->orderBy('date_heure_debut')
                ->limit(10)
                ->get();
        }

        // Grouper les séances par film et par jour
        $seancesParFilm = $seances->groupBy('film_id')->map(function ($seancesFilm) {
            $film = $seancesFilm->first()->film;

            // Grouper par jour
            $seancesParJour = $seancesFilm->groupBy(function ($seance) {
                return CarbonImmutable::parse($seance->date_heure_debut)->format('Y-m-d');
            })->map(function ($seancesJour, $date) {
                return [
                    'date'           => CarbonImmutable::parse($date),
                    'date_formatted' => CarbonImmutable::parse($date)->locale('fr')->isoFormat('dddd D MMMM'),
                    'seances'        => $seancesJour->map(function ($seance) {
                        return [
                            'id'                 => (int) $seance->id, // Force cast to int
                            'sqid'               => $seance->sqid,
                            'heure'              => $seance->getHeureDebut(),
                            'salle'              => $seance->salle->nom,
                            'version'            => $seance->getVersionComplete(),
                            'qualite'            => $seance->qualite_projection?->value ?? 'standard',
                            'places_disponibles' => $seance->places_disponibles,
                            'prix'               => $seance->getPrixFormate(),
                        ];
                    })->sortBy('heure')->values(),
                ];
            })->sortKeys()->values();

            return [
                'film' => [
                    'id'             => $film->id,
                    'titre'          => $film->titre,
                    'duree'          => $film->getDureeFormatee(),
                    'genre'          => $film->categorie?->value  ?? 'Non classé',
                    'classification' => $film->classification_age ?? '',
                    'affiche'        => $film->getPosterUrl(),
                    'note_moyenne'   => $film->note_moyenne ? round($film->note_moyenne, 1) : null,
                ],
                'seances_par_jour' => $seancesParJour,
            ];
        })->values();

        // Récupérer 4 films aléatoires pour la section "Prochainement"
        $filmsProchainement = \App\Models\Film::whereHas('seances', function ($query) use ($cinema, $dateFin) {
            $query->whereHas('salle', function ($q) use ($cinema) {
                $q->where('cinema_id', $cinema->id);
            })
                ->where('date_heure_debut', '>', $dateFin)
                ->where('etat', \App\Domain\Enums\EtatSeance::Programmee);
        })
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($film) {
                return [
                    'id'             => $film->id,
                    'titre'          => $film->titre,
                    'duree'          => $film->getDureeFormatee(),
                    'genre'          => $film->categorie?->value ?? 'Non classé',
                    'affiche'        => $film->getPosterUrl(),
                    'note_moyenne'   => $film->note_moyenne ? round($film->note_moyenne, 1) : null,
                ];
            });

        // Informations pratiques formatées
        $informationsPratiques = $this->formatInformationsPratiques($cinema);

        return view('app.cinemas.show', [
            'cinema'                => $cinema,
            'seancesParFilm'        => $seancesParFilm,
            'filmsProchainement'    => $filmsProchainement,
            'informationsPratiques' => $informationsPratiques,
            'coordonneesGPS'        => $cinema->coordonnees_gps ?? ['latitude' => 0, 'longitude' => 0],
        ]);
    }

    private function formatInformationsPratiques(Cinema $cinema): array
    {
        // Services disponibles avec icônes
        $services = [];

        if (in_array('parking', $cinema->services)) {
            $services[] = [
                'nom'         => 'Parking',
                'description' => 'Parking gratuit disponible',
                'icon'        => 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1',
            ];
        }

        if (in_array('accessibilite', $cinema->services)) {
            $services[] = [
                'nom'         => 'Accès PMR',
                'description' => 'Accès facilité pour personnes à mobilité réduite',
                'icon'        => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            ];
        }

        if (in_array('restaurant', $cinema->services)) {
            $services[] = [
                'nom'         => 'Restaurant',
                'description' => 'Restaurant sur place',
                'icon'        => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
            ];
        }

        if (in_array('bar', $cinema->services)) {
            $services[] = [
                'nom'         => 'Bar',
                'description' => 'Bar et snacks disponibles',
                'icon'        => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            ];
        }

        if (in_array('boutique', $cinema->services)) {
            $services[] = [
                'nom'         => 'Boutique',
                'description' => 'Boutique de produits dérivés',
                'icon'        => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z',
            ];
        }

        // Horaires formatés par jour
        $horairesFormates = [];
        $jours            = [
            'lundi'    => 'Lundi',
            'mardi'    => 'Mardi',
            'mercredi' => 'Mercredi',
            'jeudi'    => 'Jeudi',
            'vendredi' => 'Vendredi',
            'samedi'   => 'Samedi',
            'dimanche' => 'Dimanche',
        ];

        foreach ($jours as $key => $label) {
            if (isset($cinema->horaires_ouverture[$key])) {
                $horaire            = $cinema->horaires_ouverture[$key];
                $horairesFormates[] = [
                    'jour'     => $label,
                    'horaire'  => $horaire[0].' - '.$horaire[1],
                    'is_today' => mb_strtolower(CarbonImmutable::now()->locale('fr')->dayName) === $key,
                ];
            }
        }

        return [
            'services' => $services,
            'horaires' => $horairesFormates,
            'contact'  => [
                'telephone' => $cinema->telephone,
                'email'     => $cinema->email,
                'adresse'   => sprintf(
                    '%s, %s %s, %s',
                    $cinema->adresse['rue'],
                    $cinema->adresse['code_postal'],
                    $cinema->adresse['ville'],
                    $cinema->adresse['pays']
                ),
            ],
        ];
    }
}
