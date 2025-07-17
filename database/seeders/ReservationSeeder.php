<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Billet;
use App\Models\Cinema;
use App\Models\Reservation;
use App\Models\Seance;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require __DIR__.'/datas/reservations.php';

        $totalReservations = 0;
        $totalBillets      = 0;

        foreach ($data['reservations_par_cinema'] as $cinemaUuid => $count) {
            $cinema = Cinema::where('uuid', $cinemaUuid)->first();

            if (! $cinema) {
                echo "⚠️  Cinéma non trouvé: {$cinemaUuid}\n";

                continue;
            }

            [$resCount, $billetCount] = $this->createReservationsForCinema($cinema, $count, $data);
            $totalReservations += $resCount;
            $totalBillets += $billetCount;

            echo "✅ {$resCount} réservations et {$billetCount} billets créés pour {$cinema->nom}\n";
        }

        echo "\n🎯 Total: {$totalReservations} réservations et {$totalBillets} billets créés\n";
    }

    private function createReservationsForCinema(Cinema $cinema, int $count, array $data): array
    {
        $reservationsCreated = 0;
        $billetsCreated      = 0;

        // Récupérer les séances du cinéma
        $seances = Seance::whereHas('salle', function ($query) use ($cinema) {
            $query->where('cinema_id', $cinema->id);
        })->get();

        // Récupérer les clients
        $clients = User::where('user_type', 'client')->get();

        if ($seances->isEmpty() || $clients->isEmpty()) {
            echo "⚠️  Aucune séance ou client trouvé pour {$cinema->nom}\n";

            return [0, 0];
        }

        $codeCinema = $cinema->code_cinema;

        for ($i = 0; $i < $count; $i++) {
            $seance = $seances->random();
            $client = $clients->random();

            // Choisir nombre de places
            $nombrePlaces = $this->selectNombrePlaces($data['places_distribution']);

            // Choisir statut
            $statut = $this->selectStatut($data['statuts_distribution']);

            // Créer la réservation
            $reservation = $this->createReservation($seance, $client, $codeCinema, $nombrePlaces, $statut, $data);

            if ($reservation) {
                $reservationsCreated++;

                // Créer les billets
                $billets = $this->createBillets($reservation, $seance, $nombrePlaces, $data);
                $billetsCreated += count($billets);

                // Mettre à jour la séance (places réservées)
                $this->updateSeancePlaces($seance, $nombrePlaces, $statut);
            }
        }

        return [$reservationsCreated, $billetsCreated];
    }

    private function createReservation(
        Seance $seance,
        User $client,
        string $codeCinema,
        int $nombrePlaces,
        string $statut,
        array $data
    ): ?Reservation {
        $uuid = Str::uuid()->toString();

        // Générer numéro de réservation unique
        $numeroReservation = $this->generateNumeroReservation($codeCinema, $uuid);

        // Calculer prix total (simplifié)
        $prixTotal = $seance->prix_base * $nombrePlaces;

        // Dates selon statut
        $dates = $this->calculateDates($seance, $statut);

        // Notes selon statut
        $notes = $this->generateNotes($statut, $data);

        return Reservation::create([
            'uuid'               => $uuid,
            'user_id'            => $client->id,
            'seance_id'          => $seance->id,
            'code_cinema'        => $codeCinema,
            'numero_reservation' => $numeroReservation,
            'statut'             => $statut,
            'nombre_places'      => $nombrePlaces,
            'prix_total'         => $prixTotal,
            'confirmed_at'       => $dates['confirmed_at'],
            'expires_at'         => $dates['expires_at'],
            'notes'              => $notes,
            'created_at'         => $dates['created_at'],
            'updated_at'         => $dates['updated_at'],
        ]);
    }

    private function createBillets(Reservation $reservation, Seance $seance, int $nombrePlaces, array $data): array
    {
        $billets = [];

        // Récupérer les places déjà occupées pour cette séance
        $placesOccupees = Billet::where('seance_id', $seance->id)
            ->pluck('place')
            ->toArray();

        // Générer places disponibles
        $places = $this->generatePlaces($nombrePlaces, $data['patterns_places'], $placesOccupees);

        foreach ($places as $index => $place) {
            $typeTarif = $this->selectTarif($data['tarifs_distribution']);
            $prix      = $this->calculatePrixBillet($seance->prix_base, $typeTarif);

            $billet = Billet::create([
                'uuid'             => Str::uuid()->toString(),
                'reservation_id'   => $reservation->id,
                'seance_id'        => $seance->id,
                'numero_billet'    => $this->generateNumeroBillet($reservation->code_cinema, $reservation->id, $index + 1),
                'place'            => $place,
                'type_tarif'       => $typeTarif,
                'prix'             => $prix,
                'qr_code'          => $this->generateQRCode($reservation, $place, $data),
                'utilise'          => $this->shouldBeUsed($reservation->statut, $seance),
                'date_utilisation' => $this->getDateUtilisation($reservation->statut, $seance),
            ]);

            $billets[] = $billet;
        }

        return $billets;
    }

    private function generateNumeroReservation(string $codeCinema, string $uuid): string
    {
        // Format: CHT25D1A2B3C4E (3 lettres + année + 8 premiers chars UUID sans tirets)
        $year      = CarbonImmutable::now()->format('y');
        $uuidShort = mb_substr(str_replace('-', '', $uuid), 0, 8);

        return $codeCinema.$year.mb_strtoupper($uuidShort);
    }

    private function generateNumeroBillet(string $codeCinema, int $reservationId, int $index): string
    {
        // Format: BIL-2025-001234-01
        $year           = CarbonImmutable::now()->format('Y');
        $reservationNum = mb_str_pad((string) $reservationId, 6, '0', STR_PAD_LEFT);
        $billetNum      = mb_str_pad((string) $index, 2, '0', STR_PAD_LEFT);

        return "BIL-{$year}-{$reservationNum}-{$billetNum}";
    }

    private function generateQRCode(Reservation $reservation, string $place, array $data): string
    {
        $domain = $data['qr_domains'][array_rand($data['qr_domains'])];
        $token  = mb_substr(md5($reservation->uuid.$place.time()), 0, 16);

        return "{$domain}/verify/{$token}";
    }

    private function generatePlaces(int $nombre, array $patterns, array $placesOccupees = []): array
    {
        $places        = [];
        $rangees       = $patterns['rangees'];
        $maxTentatives = 1000; // Éviter les boucles infinies
        $tentatives    = 0;

        while (count($places) < $nombre && $tentatives < $maxTentatives) {
            // Choisir une rangée aléatoire
            $rangee = $rangees[array_rand($rangees)];

            // Choisir un siège aléatoire
            $siege = random_int(1, $patterns['sieges_max']);

            // Formater la place
            $place = $rangee.mb_str_pad((string) $siege, 2, '0', STR_PAD_LEFT);

            // Éviter les doublons dans cette réservation ET les places déjà occupées
            if (! in_array($place, $places) && ! in_array($place, $placesOccupees)) {
                $places[] = $place;
            }

            $tentatives++;
        }

        // Si impossible d'avoir assez de places, prendre ce qu'on peut
        if (count($places) < $nombre) {
            echo "⚠️  Impossible de générer {$nombre} places uniques, seulement ".count($places)." disponibles\n";
        }

        return $places;
    }

    private function selectNombrePlaces(array $distribution): int
    {
        $rand       = random_int(1, 100) / 100;
        $cumulative = 0;

        foreach ($distribution as $nombre => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $nombre;
            }
        }

        return 2; // Par défaut
    }

    private function selectStatut(array $distribution): string
    {
        $rand       = random_int(1, 100) / 100;
        $cumulative = 0;

        foreach ($distribution as $statut => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $statut;
            }
        }

        return 'payee'; // Par défaut
    }

    private function selectTarif(array $distribution): string
    {
        $rand       = random_int(1, 100) / 100;
        $cumulative = 0;

        foreach ($distribution as $tarif => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $tarif;
            }
        }

        return 'plein'; // Par défaut
    }

    private function calculatePrixBillet(float $prixBase, string $typeTarif): float
    {
        return match ($typeTarif) {
            'plein'    => $prixBase,
            'reduit'   => round($prixBase * 0.85, 2),
            'etudiant' => round($prixBase * 0.8, 2),
            'senior'   => round($prixBase * 0.85, 2),
            'enfant'   => round($prixBase * 0.7, 2),
            'groupe'   => round($prixBase * 0.9, 2),
            default    => $prixBase,
        };
    }

    private function calculateDates(Seance $seance, string $statut): array
    {
        // Disperser les créations sur les 60 derniers jours
        $createdAt   = CarbonImmutable::now()->subDays(random_int(1, 60));
        $updatedAt   = $createdAt->copy();
        $confirmedAt = null;
        $expiresAt   = null;

        switch ($statut) {
            case 'payee':
            case 'terminee':
                $confirmedAt = $createdAt->copy()->addMinutes(random_int(5, 120));
                $updatedAt   = $confirmedAt->copy()->addMinutes(random_int(1, 30));
                break;

            case 'confirmee':
                $confirmedAt = $createdAt->copy()->addMinutes(random_int(5, 60));
                $expiresAt   = $seance->date_heure_debut->copy()->subHours(2);
                $updatedAt   = $confirmedAt->copy()->addMinutes(random_int(1, 30));
                break;

            case 'en_attente':
                $expiresAt = $createdAt->copy()->addMinutes(30);
                $updatedAt = $createdAt->copy()->addMinutes(random_int(1, 15));
                break;

            case 'expiree':
                $expiresAt = $createdAt->copy()->addMinutes(30);
                $updatedAt = $expiresAt->copy()->addMinutes(random_int(1, 10));
                break;

            case 'annulee':
                $updatedAt = $createdAt->copy()->addMinutes(random_int(5, 240));
                break;
        }

        return [
            'created_at'   => $createdAt,
            'updated_at'   => $updatedAt,
            'confirmed_at' => $confirmedAt,
            'expires_at'   => $expiresAt,
        ];
    }

    private function generateNotes(string $statut, array $data): ?string
    {
        return match ($statut) {
            'annulee'  => $data['raisons_annulation'][array_rand($data['raisons_annulation'])],
            'payee'    => random_int(1, 100) <= 20 ? $data['notes_speciales'][array_rand($data['notes_speciales'])] : null,
            'terminee' => 'Billet utilisé normalement',
            'expiree'  => 'Expirée - paiement non effectué dans les délais',
            default    => null,
        };
    }

    private function shouldBeUsed(string $statut, Seance $seance): bool
    {
        if ($statut === 'terminee') {
            return true;
        }

        // Certains billets payés sont utilisés si la séance est passée
        if ($statut === 'payee' && $seance->date_heure_debut->isPast()) {
            return random_int(1, 100) <= 80; // 80% des billets payés sont utilisés
        }

        return false;
    }

    private function getDateUtilisation(string $statut, Seance $seance): ?CarbonImmutable
    {
        if ($statut === 'terminee') {
            return CarbonImmutable::instance($seance->date_heure_debut)->addMinutes(random_int(-30, 30));
        }

        if ($statut === 'payee' && $seance->date_heure_debut->isPast() && random_int(1, 100) <= 80) {
            return CarbonImmutable::instance($seance->date_heure_debut)->addMinutes(random_int(-30, 30));
        }

        return null;
    }

    private function updateSeancePlaces(Seance $seance, int $nombrePlaces, string $statut): void
    {
        if (in_array($statut, ['payee', 'confirmee', 'terminee'])) {
            $seance->increment('places_reservees', $nombrePlaces);
            $seance->decrement('places_disponibles', $nombrePlaces);
        }
    }
}
