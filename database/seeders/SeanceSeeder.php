<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Film;
use App\Models\Salle;
use App\Models\Seance;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SeanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require __DIR__.'/datas/seances.php';

        $totalCreated = 0;

        foreach ($data['cinemas'] as $cinemaUuid => $cinemaConfig) {
            $created = $this->createSeancesForCinema($cinemaUuid, $cinemaConfig, $data);
            $totalCreated += $created;

            echo "✅ {$created} séances créées pour {$cinemaConfig['name']}\n";
        }

        echo "\n🎯 Total: {$totalCreated} séances créées sur 4 semaines\n";
    }

    private function createSeancesForCinema(string $cinemaUuid, array $cinemaConfig, array $data): int
    {
        $created = 0;

        // Récupérer les films et salles
        // Gestion du cas où le cinéma peut programmer tous les films
        if ($cinemaConfig['films_selection'] === 'all') {
            $films = Film::all();
        } else {
            $films = Film::whereIn('uuid', $cinemaConfig['films_selection'])->get();
        }

        $salles = Salle::whereIn('uuid', $cinemaConfig['salles'])->get();

        if ($films->isEmpty() || $salles->isEmpty()) {
            echo "⚠️  Aucun film ou salle trouvé pour {$cinemaConfig['name']}\n";

            return 0;
        }

        // Pour chaque période (4 semaines)
        foreach ($data['periods'] as $period) {
            $periodCreated = $this->createSeancesForPeriod(
                $period,
                $cinemaConfig,
                $films,
                $salles,
                $data
            );
            $created += $periodCreated;
        }

        return $created;
    }

    private function createSeancesForPeriod(
        array $period,
        array $cinemaConfig,
        $films,
        $salles,
        array $data
    ): int {
        $created   = 0;
        $startDate = $period['start'];

        // Pour chaque jour de la semaine (mercredi à mardi)
        for ($day = 0; $day < 7; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            $dayName     = $this->getDayScheduleKey($currentDate);
            $horaires    = $data['horaires'][$dayName];

            // Sélectionner quelques films pour ce jour (pas tous)
            $dailyFilms = $films->shuffle()->take(random_int(3, min(6, $films->count())));

            foreach ($dailyFilms as $film) {
                // Choisir une salle aléatoire
                $salle = $salles->random();

                // Choisir 1-2 horaires pour ce film
                $filmHoraires = collect($horaires)->shuffle()->take(random_int(1, 2));

                foreach ($filmHoraires as $horaire) {
                    $seanceDateTime = $currentDate->copy()->setTimeFromTimeString($horaire);

                    // Éviter les conflits dans la même salle
                    if ($this->hasConflict($salle->uuid, $seanceDateTime, $film->duree_minutes)) {
                        continue;
                    }

                    $this->createSeance($film, $salle, $seanceDateTime, $cinemaConfig, $data);
                    $created++;
                }
            }
        }

        return $created;
    }

    private function createSeance(
        Film $film,
        Salle $salle,
        CarbonImmutable $dateTime,
        array $cinemaConfig,
        array $data
    ): void {
        $finDateTime = $dateTime->copy()->addMinutes($film->duree_minutes + 30); // +30min nettoyage

        // Déterminer la qualité de projection
        $qualite = $this->determineQualite($film, $salle, $dateTime, $cinemaConfig, $data);

        // Prix selon la qualité
        $prix = $data['tarifs'][$qualite];

        // Version (VF/VO/VOST)
        $version = $this->selectVersion($data['versions']);

        // Capacité de la salle (simulée selon le nom)
        $capacite = $this->getSalleCapacite($salle);

        Seance::create([
            'uuid'               => Str::uuid()->toString(),
            'film_id'            => $film->id,
            'salle_id'           => $salle->id,
            'date_heure_debut'   => $dateTime,
            'date_heure_fin'     => $finDateTime,
            'etat'               => 'programmee',
            'places_disponibles' => $capacite,
            'places_reservees'   => 0,
            'qualite_projection' => $qualite,
            'prix_base'          => $prix,
            'tarifs_speciaux'    => [
                'etudiant' => round($prix * 0.8, 2),
                'senior'   => round($prix * 0.85, 2),
                'enfant'   => round($prix * 0.7, 2),
            ],
            'version'      => $version,
            'sous_titres'  => $version === 'VOST',
            'langue_audio' => $version === 'VO' ? 'anglais' : 'français',
        ]);
    }

    private function determineQualite(
        Film $film,
        Salle $salle,
        CarbonImmutable $dateTime,
        array $cinemaConfig,
        array $data
    ): string {
        $horaire             = $dateTime->format('H:i');
        $qualitesDisponibles = $cinemaConfig['qualites'];

        // Règles spéciales IMAX
        if (in_array('imax', $qualitesDisponibles) &&
            str_contains($salle->nom, 'IMAX') &&
            in_array($horaire, $data['qualite_rules']['imax']['horaires']) &&
            in_array($film->categorie, $data['qualite_rules']['imax']['films_types'])) {
            return 'imax';
        }

        // Règles 3D
        if (in_array('3d', $qualitesDisponibles) &&
            in_array($horaire, $data['qualite_rules']['3d']['horaires']) &&
            in_array($film->categorie, $data['qualite_rules']['3d']['films_types']) &&
            random_int(1, 100) <= 40) { // 40% chance
            return '3d';
        }

        // Règles Dolby Atmos
        if (in_array('dolby_atmos', $qualitesDisponibles) &&
            in_array($horaire, $data['qualite_rules']['dolby_atmos']['horaires']) &&
            in_array($film->categorie, $data['qualite_rules']['dolby_atmos']['films_types']) &&
            random_int(1, 100) <= 30) { // 30% chance
            return 'dolby_atmos';
        }

        // Règles 4K
        if (in_array('4k', $qualitesDisponibles) &&
            in_array($horaire, $data['qualite_rules']['4k']['horaires']) &&
            random_int(1, 100) <= 50) { // 50% chance
            return '4k';
        }

        // Par défaut
        return 'standard';
    }

    private function selectVersion(array $versions): string
    {
        $rand       = random_int(1, 100) / 100;
        $cumulative = 0;

        foreach ($versions as $version => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $version;
            }
        }

        return 'VF';
    }

    private function getSalleCapacite(Salle $salle): int
    {
        // Capacités simulées selon le nom de la salle
        if (str_contains($salle->nom, 'IMAX')) {
            return 400;
        }
        if (str_contains($salle->nom, 'Premium')) {
            return 300;
        }
        if (str_contains($salle->nom, 'Grand')) {
            return 250;
        }
        if (str_contains($salle->nom, 'Méliès') || str_contains($salle->nom, 'Lumière')) {
            return 200;
        }
        if (str_contains($salle->nom, 'Capitole')) {
            return 180;
        }

        // Capacité par défaut selon numéro/taille
        return random_int(80, 200);
    }

    private function getDayScheduleKey(CarbonImmutable $date): string
    {
        $dayOfWeek = $date->dayOfWeek;

        return match ($dayOfWeek) {
            CarbonImmutable::MONDAY, CarbonImmutable::TUESDAY, CarbonImmutable::WEDNESDAY, CarbonImmutable::THURSDAY => 'lundi_mardi_mercredi_jeudi',
            CarbonImmutable::FRIDAY   => 'vendredi',
            CarbonImmutable::SATURDAY => 'samedi',
            CarbonImmutable::SUNDAY   => 'dimanche',
            default                   => 'lundi_mardi_mercredi_jeudi',
        };
    }

    private function hasConflict(string $salleUuid, CarbonImmutable $dateTime, int $dureeMinutes): bool
    {
        $finDateTime = $dateTime->copy()->addMinutes($dureeMinutes + 30);

        return Seance::whereHas('salle', function ($query) use ($salleUuid) {
            $query->where('uuid', $salleUuid);
        })
            ->where(function ($query) use ($dateTime, $finDateTime) {
                $query->whereBetween('date_heure_debut', [$dateTime->subMinutes(30), $finDateTime])
                    ->orWhereBetween('date_heure_fin', [$dateTime, $finDateTime->addMinutes(30)]);
            })
            ->exists();
    }
}
