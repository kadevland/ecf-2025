<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\CinemaFilterRequest;
use App\Models\Cinema;

final class CinemaController extends Controller
{
    public function __invoke(CinemaFilterRequest $request): \Illuminate\Contracts\View\View
    {
        // Récupérer les filtres validés
        $pays   = $request->validated('pays');
        $search = $request->validated('search');

        // Query de base
        $query = Cinema::where('statut', 'actif');

        // Appliquer les filtres
        if ($pays && $pays !== 'tous') {
            $query->where('adresse->pays', $pays);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'ilike', '%'.$search.'%')
                    ->orWhere('adresse->ville', 'ilike', '%'.$search.'%');
            });
        }

        // Récupérer les cinémas filtrés
        $cinemas = $query->orderBy('nom')->get()
            ->map(function (Cinema $cinema) {
                // Compter les salles pour ce cinéma
                $nombreSalles = $cinema->salles()->count();

                // Formater les horaires
                $horaires = $this->formatHoraires($cinema->horaires_ouverture);

                // Formater l'adresse complète
                $adresseComplete = sprintf(
                    '%s, %s %s, %s',
                    $cinema->adresse['rue'],
                    $cinema->adresse['code_postal'],
                    $cinema->adresse['ville'],
                    $cinema->adresse['pays']
                );

                return [
                    'id'               => $cinema->id,
                    'uuid'             => $cinema->uuid,
                    'model'            => $cinema, // Passer le modèle complet pour les routes
                    'nom'              => $cinema->nom,
                    'description'      => $cinema->description,
                    'adresse'          => $cinema->adresse['rue'],
                    'adresse_complete' => $adresseComplete,
                    'ville'            => $cinema->adresse['ville'],
                    'code_postal'      => $cinema->adresse['code_postal'],
                    'pays'             => $cinema->adresse['pays'],
                    'telephone'        => $cinema->telephone,
                    'email'            => $cinema->email,
                    'nombre_salles'    => $nombreSalles,
                    'parking'          => in_array('parking', $cinema->services),
                    'acces_pmr'        => in_array('accessibilite', $cinema->services),
                    'restaurant'       => in_array('restaurant', $cinema->services),
                    'boutique'         => in_array('boutique', $cinema->services),
                    'bar'              => in_array('bar', $cinema->services),
                    'horaires'         => $horaires,
                    'coordonnees'      => $cinema->coordonnees_gps,
                    'photo'            => 'https://picsum.photos/400/300?random='.$cinema->id, // Photos aléatoires réalistes
                ];
            });

        // Grouper par pays pour faciliter le filtrage
        $allCinemas       = Cinema::where('statut', 'actif')->get();
        $cinemasByCountry = $allCinemas->groupBy(fn ($cinema) => $cinema->adresse['pays']);

        return view('app.cinemas.index', [
            'cinemas'          => $cinemas,
            'allCinemasCount'  => $allCinemas->count(),
            'cinemasByCountry' => $cinemasByCountry,
            'selectedPays'     => $pays,
            'search'           => $search,
        ]);
    }

    private function formatHoraires(array $horaires): string
    {
        // Trouver les horaires les plus communs
        $horairesCounts = [];
        foreach ($horaires as $jour => $horaire) {
            $key                  = $horaire[0].'-'.$horaire[1];
            $horairesCounts[$key] = ($horairesCounts[$key] ?? 0) + 1;
        }

        // Prendre les horaires les plus fréquents
        $horairePrincipal        = array_keys($horairesCounts, max($horairesCounts))[0];
        [$ouverture, $fermeture] = explode('-', $horairePrincipal);

        // Vérifier s'il y a des horaires spéciaux le weekend
        $weekendDifferent = (
            $horaires['vendredi'][1] !== $horaires['lundi'][1] ||
            $horaires['samedi'][1] !== $horaires['lundi'][1]
        );

        if ($weekendDifferent) {
            return sprintf(
                'Lun-Jeu %s-%s, Ven-Sam %s-%s, Dim %s-%s',
                $ouverture,
                $horaires['lundi'][1],
                $horaires['vendredi'][0],
                $horaires['vendredi'][1],
                $horaires['dimanche'][0],
                $horaires['dimanche'][1]
            );
        }

        return sprintf('Tous les jours %s - %s', $ouverture, $fermeture);
    }
}
