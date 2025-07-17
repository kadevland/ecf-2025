<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Seance;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ReservationController extends Controller
{
    /**
     * Étape 1 : Sélection des places
     */
    public function selectSeats(Seance $seance): View
    {
        // Charger les relations
        $seance->load(['film', 'salle.cinema']);

        // Créer le ViewModel pour gérer tous les cas
        $viewModel = $this->creerViewModel($seance);

        return view('app.reservation.select-seats', $viewModel);
    }

    /**
     * Étape 2 : Afficher la page de confirmation (GET)
     */
    public function showConfirmation(Request $request): View
    {
        // Vérifier que les données de session existent
        if (!session()->has('reservation_data')) {
            return redirect()->route('accueil')
                ->with('error', 'Session expirée, veuillez recommencer votre réservation.');
        }

        $reservationData = session('reservation_data');

        return view('app.reservation.confirm', [
            'reservation' => $reservationData,
        ]);
    }

    /**
     * Étape 2 : Traiter la confirmation (POST)
     */
    public function confirmReservation(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'seance_id' => 'required|exists:seances,id',
            'seats'     => 'required|array',
            'seats.*'   => 'required|string|regex:/^[A-Z]{1}[0-9]{1,2}$/', // Format A1, B12, etc.
        ]);

        $seance = Seance::with(['film', 'salle.cinema'])->findOrFail($request->seance_id);

        // Récupérer les places depuis l'array
        $placesSelectionnees = $request->seats;

        // Vérifier que les places sont encore disponibles
        $placesOccupees = \App\Models\Billet::where('seance_id', $seance->id)
            ->whereIn('place', $placesSelectionnees)
            ->pluck('place')
            ->toArray();

        if (!empty($placesOccupees)) {
            return redirect()->route('reservation.select-seats', $seance->sqid)
                ->with('error', 'Les places suivantes ne sont plus disponibles : ' . implode(', ', $placesOccupees));
        }

        $nombrePlaces = count($placesSelectionnees);
        $prixTotal    = $seance->prix_base * $nombrePlaces;

        $reservationData = [
            'seance'        => [
                'id'         => $seance->id,
                'film_titre' => $seance->film->titre,
                'cinema_nom' => $seance->salle->cinema->nom,
                'salle_nom'  => $seance->salle->nom,
                'date_heure' => CarbonImmutable::parse($seance->date_heure_debut)->locale('fr')
                    ->isoFormat('dddd D MMMM YYYY [à] HH:mm'),
                'version'    => $seance->getVersionComplete(),
            ],
            'places'        => $placesSelectionnees,
            'nombre_places' => $nombrePlaces,
            'prix_unitaire' => $seance->prix_base,
            'prix_total'    => $prixTotal,
        ];

        // Stocker les données en session
        session(['reservation_data' => $reservationData]);

        // Rediriger vers la page de confirmation
        return redirect()->route('reservation.show-confirmation');
    }

    /**
     * Étape 3 : Finalisation de la réservation
     */
    public function finalize(Request $request): View|\Illuminate\Http\RedirectResponse
    {

        $request->validate([
            'seance_id' => 'required|exists:seances,id',
            'seats'     => 'required|array',
            'seats.*'   => 'required|string|regex:/^[A-Z]{1}[0-9]{1,2}$/', // Format A1, B12, etc.
        ]);

        // Simuler un paiement (pour l'instant, toujours OK)
        // $paiementReussi = true;

        // if (!$paiementReussi) {
        //     // Rediriger vers une page d'erreur de paiement
        //     return redirect()->back()
        //         ->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        // }

        // Obtenir l'utilisateur connecté
        $clientId = auth()->id();

        $seance = Seance::with(['film', 'salle.cinema'])->findOrFail($request->seance_id);

        // Récupérer les places depuis l'array
        $placesSelectionnees = $request->seats;

        // Vérifier que les places sont encore disponibles
        $placesOccupees = \App\Models\Billet::where('seance_id', $seance->id)
            ->whereIn('place', $placesSelectionnees)
            ->pluck('place')
            ->toArray();

        if (!empty($placesOccupees)) {
            return redirect()->route('reservation.select-seats', $seance->sqid)
                ->with('error', 'Les places suivantes ne sont plus disponibles : ' . implode(', ', $placesOccupees));
        }

        // Générer le numéro de réservation
        $reservationUuid   = \Illuminate\Support\Str::uuid();
        $numeroReservation = $this->genererNumeroReservation(
            $seance->salle->cinema->code_cinema,
            $seance->id,
            $reservationUuid->toString()
        );

        $nombrePlaces = count($placesSelectionnees);
        $prixTotal    = $seance->prix_base * $nombrePlaces;

        // Créer la réservation en base de données
        $reservation = \App\Models\Reservation::create([
            'uuid'               => $reservationUuid,
            'numero_reservation' => $numeroReservation,
            'user_id'            => $clientId,
            'seance_id'          => $seance->id,
            'code_cinema'        => $seance->salle->cinema->code_cinema,
            'nombre_places'      => $nombrePlaces,
            'prix_total'         => $prixTotal,
            'statut'             => \App\Domain\Enums\StatutReservation::Confirmee,
            'confirmed_at'       => now(),
        ]);

        // Créer les billets pour chaque place (un billet = une place)
        foreach ($placesSelectionnees as $place) {
            \App\Models\Billet::create([
                'uuid'           => \Illuminate\Support\Str::uuid(),
                'reservation_id' => $reservation->id,
                'seance_id'      => $seance->id,
                'numero_billet'  => $numeroReservation . '-' . $place,
                'place'          => $place,
                'type_tarif'     => 'plein',
                'prix'           => $seance->prix_base,
                'qr_code'        => 'QR_' . $reservation->uuid . '_' . $place,
                'utilise'        => false,
            ]);
        }

        // Recharger la réservation avec ses relations pour l'affichage
        $reservation->load(['client', 'seance.film', 'seance.salle.cinema', 'billets']);

        $reservationData = [
            'numero'           => $reservation->numero_reservation,
            'seance'           => [
                'film_titre'     => $reservation->seance->film->titre,
                'cinema_nom'     => $reservation->seance->salle->cinema->nom,
                'cinema_adresse' => $reservation->seance->salle->cinema->getAdresseComplete(),
                'salle_nom'      => $reservation->seance->salle->nom,
                'date_heure'     => CarbonImmutable::parse($reservation->seance->date_heure_debut)->locale('fr')
                    ->isoFormat('dddd D MMMM YYYY [à] HH:mm'),
                'version'        => $reservation->seance->getVersionComplete(),
            ],
            'client'           => [
                'nom'       => $reservation->client->last_name ?? $reservation->user->name,
                'prenom'    => $reservation->client->first_name ?? '',
                'email'     => $reservation->user->email,
                'telephone' => $reservation->client->phone ?? 'Non renseigné',
            ],
            'places'           => $placesSelectionnees,
            'nombre_places'    => $nombrePlaces,
            'prix_total'       => $prixTotal,
            'date_reservation' => $reservation->created_at->locale('fr')
                ->isoFormat('D MMMM YYYY [à] HH:mm'),
        ];

        return view('app.reservation.success', [
            'reservation' => $reservationData,
        ]);
    }

    /**
     * Génère un plan de salle avec les vraies places occupées
     */
    private function genererPlanSalle($seance): array
    {
        $salle     = $seance->salle;
        $planSalle = $salle->plan_salle;

        // Utiliser les données réelles du plan de salle
        $rangees         = $planSalle['rangees'] ?? 8;
        $siegesParRangee = min(30, $planSalle['sieges_par_rangee'] ?? 15);
        $placesPMR       = $planSalle['pmr'] ?? 2;

        $rangeesParDefaut = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
        $rangeesUtilisees = array_slice($rangeesParDefaut, 0, $rangees);

        $plan                 = [];
        $placesPMRDistribuees = 0;

        foreach ($rangeesUtilisees as $index => $rangee) {
            $siegesRangee = [];

            for ($numero = 1; $numero <= $siegesParRangee; $numero++) {
                $siegeId = $rangee . $numero;

                // Distribuer les places PMR dans la première rangée
                $pmr = $rangee === 'A' && $placesPMRDistribuees < $placesPMR && $numero <= $placesPMR;
                if ($pmr) {
                    $placesPMRDistribuees++;
                }

                $siegesRangee[] = [
                    'id'     => $siegeId,
                    'numero' => $numero,
                    'occupe' => false, // Sera mis à jour dans creerViewModel
                    'pmr'    => $pmr,
                    'prix'   => 12.0, // Prix unique pour la démo
                ];
            }

            $plan[] = [
                'rangee' => $rangee,
                'sieges' => $siegesRangee,
            ];
        }

        return $plan;
    }

    /**
     * Crée le ViewModel pour la sélection des places
     */
    private function creerViewModel(Seance $seance): array
    {
        // Vérifications de disponibilité
        $reservationPossible = true;
        $messageErreur       = '';
        $typeErreur          = '';

        // Vérifier l'état de la séance
        if ($seance->etat !== \App\Domain\Enums\EtatSeance::Programmee) {
            $reservationPossible = false;
            $messageErreur       = 'Cette séance a été annulée ou n\'est plus programmée.';
            $typeErreur          = 'annulee';
        }
        // Vérifier si la séance est passée
        elseif (CarbonImmutable::parse($seance->date_heure_debut)->isPast()) {
            $reservationPossible = false;
            $messageErreur       = 'Cette séance est déjà passée.';
            $typeErreur          = 'passee';
        }
        // Vérifier s'il reste des places
        elseif ($seance->places_disponibles <= 0) {
            $reservationPossible = false;
            $messageErreur       = 'Désolé, cette séance affiche complet.';
            $typeErreur          = 'complet';
        }

        // Données de la séance (toujours utiles pour l'affichage)
        $seanceData = [
            'id'                 => $seance->id,
            'film'               => [
                'titre'   => $seance->film->titre,
                'duree'   => $seance->film->getDureeFormatee(),
                'affiche' => $seance->film->getPosterUrl(),
            ],
            'cinema'             => [
                'nom'     => $seance->salle->cinema->nom,
                'adresse' => $seance->salle->cinema->getAdresseComplete(),
            ],
            'salle'              => [
                'nom'      => $seance->salle->nom,
                'capacite' => $seance->salle->capacite ?? 100,
            ],
            'date_heure'         => CarbonImmutable::parse($seance->date_heure_debut)->locale('fr')
                ->isoFormat('dddd D MMMM YYYY [à] HH:mm'),
            'version'            => $seance->getVersionComplete(),
            'qualite'            => $seance->qualite_projection?->value ?? 'Standard',
            'prix_base'          => $seance->prix_base,
            'places_disponibles' => $seance->places_disponibles,
        ];

        $viewModel = [
            'reservationPossible' => $reservationPossible,
            'messageErreur'       => $messageErreur,
            'typeErreur'          => $typeErreur,
            'seance'              => $seanceData,
        ];

        // Générer le plan de salle seulement si la réservation est possible
        if ($reservationPossible) {
            $planSalle = $this->genererPlanSalle($seance);

            // Récupérer les places occupées directement depuis les billets
            $placesOccupees = \App\Models\Billet::where('seance_id', $seance->id)
                ->pluck('place')
                ->toArray();


            // Marquer les places occupées dans le plan
            foreach ($planSalle as &$rangee) {
                foreach ($rangee['sieges'] as &$siege) {
                    if (in_array($siege['id'], $placesOccupees)) {
                        $siege['occupe'] = true;
                        logger()->info("Place marquée comme occupée: {$siege['id']}");
                    }
                }
            }

            $viewModel['planSalle'] = $planSalle;
        } else {
            // Suggestions d'alternatives
            $viewModel['suggestions'] = $this->obtenirSuggestions($seance);
        }

        return $viewModel;
    }

    /**
     * Obtient des suggestions d'alternatives pour la séance
     */
    private function obtenirSuggestions(Seance $seance): array
    {
        $suggestions = [];

        // Autres séances du même film dans les 7 prochains jours
        $autresSeances = Seance::where('film_id', $seance->film_id)
            ->where('id', '!=', $seance->id)
            ->where('etat', \App\Domain\Enums\EtatSeance::Programmee)
            ->where('date_heure_debut', '>', CarbonImmutable::now())
            ->where('date_heure_debut', '<=', CarbonImmutable::now()->addDays(7))
            ->where('places_disponibles', '>', 0)
            ->with(['salle.cinema'])
            ->orderBy('date_heure_debut')
            ->limit(3)
            ->get()
            ->map(function ($autreSeance) {
                return [
                    'id'                 => $autreSeance->id,
                    'sqid'               => $autreSeance->sqid,
                    'date_heure'         => CarbonImmutable::parse($autreSeance->date_heure_debut)->locale('fr')
                        ->isoFormat('dddd D MMMM [à] HH:mm'),
                    'cinema'             => $autreSeance->salle->cinema->nom,
                    'salle'              => $autreSeance->salle->nom,
                    'places_disponibles' => $autreSeance->places_disponibles,
                ];
            });

        if ($autresSeances->isNotEmpty()) {
            $suggestions['autresSeances'] = $autresSeances;
        }

        // URL de retour vers le film
        $suggestions['retourFilm'] = route('films.show', $seance->film);

        return $suggestions;
    }

    /**
     * Obtient un user ID aléatoire pour la simulation
     * En production, cela viendrait de Auth::user()->id
     */
    // private function getCurrentClientId(): int
    // {
    //     // Récupérer tous les users de type client
    //     $users = \App\Models\User::where('user_type', 'client')->get();

    //     if ($users->isEmpty()) {
    //         // Si pas d'users clients, récupérer n'importe quel user
    //         $user = \App\Models\User::first();
    //         if (!$user) {
    //             // Si vraiment aucun user, créer un user de test
    //             $user = \App\Models\User::create([
    //                 'uuid'              => \Illuminate\Support\Str::uuid(),
    //                 'name'              => 'Client Demo',
    //                 'email'             => 'demo@cinephoria.fr',
    //                 'email_verified_at' => now(),
    //                 'user_type'         => 'client',
    //                 'password'          => bcrypt('password'),
    //             ]);
    //         }
    //         return $user->id;
    //     }

    //     // Retourner un user client aléatoire
    //     return $users->random()
    //         ->id;
    // }

    /**
     * Génère un numéro de réservation unique
     * Format: [CODE_CINEMA][YY][M][4xUUID_Seance][4xUUID_Resa]
     * Total: 14 caractères max (3+2+1+4+4)
     */
    private function genererNumeroReservation(
        string $codeCinema,
        int $seanceId,
        string $reservationUuid
    ): string {
        // Année sur 2 chiffres
        $annee = now()->format('y');

        // Mois en 1-9OND
        $moisMapping = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'O', 'N', 'D'];
        $mois        = $moisMapping[(int) now()->format('n') - 1];

        // 4 premiers chars de l'ID de seance (converti en hexa)
        $seanceShort = mb_strtoupper(mb_substr(dechex($seanceId), 0, 4));

        // 4 premiers chars UUID reservation (sans tirets)
        $resaShort = mb_strtoupper(mb_substr(str_replace('-', '', $reservationUuid), 0, 4));

        return "{$codeCinema}{$annee}{$mois}{$seanceShort}{$resaShort}";
    }
}
