<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Employee;
use App\Models\Incident;
use App\Models\Salle;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require __DIR__.'/datas/incidents.php';

        $totalCreated = 0;

        foreach ($data['cinema_distribution'] as $cinemaUuid => $count) {
            $cinema = Cinema::where('uuid', $cinemaUuid)->first();

            if (! $cinema) {
                echo "⚠️  Cinéma non trouvé: {$cinemaUuid}\n";

                continue;
            }

            $created = $this->createIncidentsForCinema($cinema, $count, $data);
            $totalCreated += $created;

            echo "✅ {$created} incidents créés pour {$cinema->nom}\n";
        }

        echo "\n🎯 Total: {$totalCreated} incidents créés (tous résolus)\n";
    }

    private function createIncidentsForCinema(Cinema $cinema, int $count, array $data): int
    {
        $employees = Employee::where('cinema_id', $cinema->id)
            ->with('user')
            ->get();

        $salles = Salle::where('cinema_id', $cinema->id)->get();

        if ($employees->isEmpty()) {
            echo "⚠️  Aucun employé trouvé pour {$cinema->nom}\n";

            return 0;
        }

        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $this->createIncident($cinema, $employees, $salles, $data);
            $created++;
        }

        return $created;
    }

    private function createIncident(Cinema $cinema, $employees, $salles, array $data): void
    {
        // Choisir un type d'incident aléatoire
        $types = array_keys($data['incidents_template']);
        $type  = $types[array_rand($types)];

        // Choisir un template d'incident de ce type
        $templates = $data['incidents_template'][$type];
        $template  = $templates[array_rand($templates)];

        // Générer des données contextuelles
        $context = $this->generateContext($cinema, $salles, $data);

        // Remplacer les placeholders
        $titre        = $this->replacePlaceholders($template['titre'], $context);
        $description  = $this->replacePlaceholders($template['description'], $context);
        $localisation = $this->replacePlaceholders($template['localisation'], $context);
        $solution     = $this->replacePlaceholders($template['solution'], $context);
        $commentaires = $this->replacePlaceholders($template['commentaires'], $context);

        // Choisir des employés
        $rapporteur = $employees->random()->user;
        $assigné    = $employees->random()->user;

        // Générer des dates réalistes (incidents passés)
        $dateCreation   = CarbonImmutable::now()->subDays(random_int(1, 90));
        $dateResolution = $dateCreation->addHours(random_int(1, 48));

        // Choisir une salle si nécessaire
        $salleId = null;
        if (str_contains($localisation, 'Salle') && $salles->isNotEmpty()) {
            $salleId = $salles->random()->id;
        }

        Incident::create([
            'uuid'                  => Str::uuid()->toString(),
            'titre'                 => $titre,
            'description'           => $description,
            'type'                  => $type,
            'priorite'              => $template['priorite'],
            'statut'                => 'resolu', // Tous résolus pour l'affichage
            'localisation'          => $localisation,
            'rapporte_par_id'       => $rapporteur->id,
            'cinema_id'             => $cinema->id,
            'salle_id'              => $salleId,
            'assigne_a_id'          => $assigné->id,
            'solution_apportee'     => $solution,
            'commentaires_internes' => $commentaires,
            'resolue_at'            => $dateResolution,
            'created_at'            => $dateCreation,
            'updated_at'            => $dateResolution,
        ]);
    }

    private function generateContext(Cinema $cinema, $salles, array $data): array
    {
        $salle = $salles->isNotEmpty() ? $salles->random() : null;

        return [
            'salle'  => $salle ? $salle->nom : 'Salle '.random_int(1, 10),
            'numero' => $data['numeros'][array_rand($data['numeros'])],
            'heure'  => $data['horaires'][array_rand($data['horaires'])],
            'niveau' => $data['niveaux'][array_rand($data['niveaux'])],
            'genre'  => $data['genres'][array_rand($data['genres'])],
            'cinema' => $cinema->nom,
        ];
    }

    private function replacePlaceholders(string $text, array $context): string
    {
        foreach ($context as $key => $value) {
            $text = str_replace("{{$key}}", (string) $value, $text);
        }

        return $text;
    }
}
