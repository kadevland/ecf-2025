<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Enums\StatutReservation;
use App\Models\Billet;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\Seance;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ClientDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createDemoClient();

        echo "✅ Client démo créé avec des réservations\n";
    }

    private function createDemoClient(): void
    {
        // Vérifier si le client existe déjà
        $existingUser = User::where('email', 'demo@ecf.com')->first();

        if ($existingUser) {
            echo "⚠️  Client demo@ecf.com existe déjà\n";

            return;
        }

        // Créer l'utilisateur
        $user = User::create([
            'uuid'              => Str::uuid()->toString(),
            'email'             => 'demo@ecf.com',
            'user_type'         => 'client',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Créer le profil client
        $client = Client::create([
            'uuid'       => Str::uuid()->toString(),
            'user_id'    => $user->id,
            'first_name' => 'Demo',
            'last_name'  => 'ECF',
            'phone'      => '0123456789',
            'birth_date' => '1990-01-01',
        ]);

        // Mettre à jour le profile_id pour la relation polymorphe
        $user->profile_id = $client->id;
        $user->save();

        // Créer le mot de passe
        DB::table('user_passwords')->insert([
            'user_id'       => $user->id,
            'password_hash' => Hash::make('azerty1234!'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Créer des réservations de démonstration
        $this->createDemoReservations($user->id);

        echo "✅ Client démo créé - demo@ecf.com / azerty1234!\n";
    }

    private function createDemoReservations(int $userId): void
    {
        // Récupérer quelques séances pour créer des réservations
        $seances = Seance::with(['film', 'salle.cinema'])
            ->where('date_heure_debut', '>', now()->subDays(30))
            ->where('date_heure_debut', '<', now()->addDays(30))
            ->limit(5)
            ->get();

        if ($seances->isEmpty()) {
            echo "⚠️  Aucune séance trouvée pour créer des réservations\n";

            return;
        }

        $reservationsCreated = 0;

        foreach ($seances as $index => $seance) {
            // Créer une réservation pour chaque séance
            $reservationUuid   = Str::uuid();
            $numeroReservation = $this->genererNumeroReservation(
                $seance->salle->cinema->code_cinema,
                $seance->id,
                $reservationUuid->toString()
            );

            // Déterminer le statut selon l'index
            $statut = match ($index) {
                0       => StatutReservation::Confirmee,
                1       => StatutReservation::Payee,
                2       => StatutReservation::Terminee,
                3       => StatutReservation::Confirmee,
                default => StatutReservation::Payee
            };

            // Nombre de places aléatoire (1 à 3)
            $nombrePlaces = rand(1, 3);
            $prixTotal    = $seance->prix_base * $nombrePlaces;

            // Créer la réservation
            $reservation = Reservation::create([
                'uuid'               => $reservationUuid,
                'numero_reservation' => $numeroReservation,
                'user_id'            => $userId,
                'seance_id'          => $seance->id,
                'code_cinema'        => $seance->salle->cinema->code_cinema,
                'nombre_places'      => $nombrePlaces,
                'prix_total'         => $prixTotal,
                'statut'             => $statut,
                'confirmed_at'       => now()->subDays(rand(1, 30)),
            ]);

            // Créer les billets
            $places = ['A'.($index + 1), 'A'.($index + 2), 'A'.($index + 3)];

            for ($i = 0; $i < $nombrePlaces; $i++) {
                Billet::create([
                    'uuid'           => Str::uuid(),
                    'reservation_id' => $reservation->id,
                    'seance_id'      => $seance->id,
                    'numero_billet'  => $numeroReservation.'-'.$places[$i],
                    'place'          => $places[$i],
                    'type_tarif'     => 'plein',
                    'prix'           => $seance->prix_base,
                    'qr_code'        => 'QR_'.$reservation->uuid.'_'.$places[$i],
                    'utilise'        => $statut === StatutReservation::Terminee,
                ]);
            }

            $reservationsCreated++;
        }

        echo "✅ {$reservationsCreated} réservations créées pour le client démo\n";
    }

    /**
     * Génère un numéro de réservation unique
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
