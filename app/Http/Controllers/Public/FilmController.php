<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Carbon\CarbonImmutable;

final class FilmController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Contracts\View\View
    {
        // Calculer la période mercredi à mercredi
        $aujourd_hui  = CarbonImmutable::today();
        $jour_semaine = $aujourd_hui->dayOfWeek; // 0 = dimanche, 1 = lundi, ..., 3 = mercredi

        // Trouver le mercredi de cette semaine ou le précédent
        if ($jour_semaine >= 3) {
            // Si on est mercredi ou après, prendre le mercredi de cette semaine
            $mercredi_debut = $aujourd_hui->startOfWeek()->addDays(2); // mercredi
        } else {
            // Si on est avant mercredi, prendre le mercredi de la semaine précédente
            $mercredi_debut = $aujourd_hui->subWeek()->startOfWeek()->addDays(2);
        }

        $mercredi_fin = $mercredi_debut->addDays(6)->endOfDay(); // mardi suivant à 23:59

        // Construire la requête avec les filtres - seulement les films avec séances sur la période
        $query = Film::whereHas('seances', function ($q) use ($mercredi_debut, $mercredi_fin) {
            $q->whereBetween('date_heure_debut', [$mercredi_debut, $mercredi_fin])
                ->where('etat', 'programmee');
        });

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->get('recherche');
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'ilike', '%'.$recherche.'%')
                    ->orWhere('realisateur', 'ilike', '%'.$recherche.'%')
                    ->orWhere('description', 'ilike', '%'.$recherche.'%');
            });
        }

        // Catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->get('categorie'));
        }

        // Tri
        $tri = $request->get('tri', 'titre_asc');
        switch ($tri) {
            case 'titre_desc':
                $query->orderBy('titre', 'desc');
                break;
            case 'note_desc':
                $query->orderByRaw('COALESCE(note_moyenne, 0) DESC');
                break;
            case 'note_asc':
                $query->orderByRaw('COALESCE(note_moyenne, 0) ASC');
                break;
            case 'date_desc':
                $query->orderBy('date_sortie', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('date_sortie', 'asc');
                break;
            case 'titre_asc':
            default:
                $query->orderBy('titre', 'asc');
                break;
        }

        // Paginer les résultats avec eager loading des séances
        $perPage = 12; // 12 films par page
        $films   = $query->with(['seances' => function ($query) use ($mercredi_debut, $mercredi_fin) {
            $query->whereBetween('date_heure_debut', [$mercredi_debut, $mercredi_fin])
                ->where('etat', 'programmee')
                ->with('salle.cinema')
                ->orderBy('date_heure_debut');
        }])->paginate($perPage)->withQueryString()
            ->through(function (Film $film) {
                // Les séances sont déjà chargées via eager loading
                $seances_periode = $film->seances
                    ->map(function ($seance) {
                        return [
                            'date'   => CarbonImmutable::parse($seance->date_heure_debut)->format('d/m'),
                            'heure'  => CarbonImmutable::parse($seance->date_heure_debut)->format('H:i'),
                            'cinema' => $seance->salle->cinema->nom,
                            'salle'  => $seance->salle->nom,
                        ];
                    });

                return [
                    'model'              => $film, // Pour les routes
                    'id'                 => $film->id,
                    'titre'              => $film->titre,
                    'description'        => $film->description,
                    'duree'              => $film->getDureeFormatee(),
                    'categorie'          => $film->categorie?->value ?? 'Non classé',
                    'realisateur'        => $film->realisateur,
                    'pays_origine'       => $film->pays_origine,
                    'date_sortie'        => $film->date_sortie?->format('Y'),
                    'affiche'            => $film->getPosterUrl(),
                    'note_moyenne'       => $film->note_moyenne,
                    'note_formatee'      => $film->getNoteMoyenneFormatee(),
                    'seances_periode'    => $seances_periode,
                    'a_des_seances'      => $seances_periode->isNotEmpty(),
                ];
            });

        // Récupérer les catégories disponibles pour les filtres
        $categories = Film::select('categorie')
            ->distinct()
            ->whereNotNull('categorie')
            ->get()
            ->map(fn ($film) => $film->categorie?->value)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('app.films.index', [
            'films'           => $films,
            'categories'      => $categories,
            'periode_debut'   => $mercredi_debut->format('d/m/Y'),
            'periode_fin'     => $mercredi_fin->format('d/m/Y'),
        ]);
    }
}
