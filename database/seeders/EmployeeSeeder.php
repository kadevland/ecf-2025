<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Employee;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require __DIR__.'/datas/employees.php';

        foreach ($data['cinema_configs'] as $cinemaUuid => $config) {
            $cinema = Cinema::where('uuid', $cinemaUuid)->first();

            if (! $cinema) {
                echo "⚠️  Cinéma non trouvé: {$cinemaUuid}\n";

                continue;
            }

            $this->createEmployeesForCinema($cinema, $config, $data);
        }
    }

    private function createEmployeesForCinema(Cinema $cinema, array $config, array $data): void
    {
        $totalEmployees = $config['employees'];
        $cinemaName     = $config['name'];

        // Calculer la répartition des postes
        $positionDistribution = $this->calculatePositionDistribution($totalEmployees, $data['positions']);

        $createdCount = 0;

        foreach ($positionDistribution as $position => $count) {
            for ($i = 0; $i < $count; $i++) {
                $this->createEmployee($cinema, $position, $data);
                $createdCount++;
            }
        }

        echo "✅ {$createdCount} employés créés pour {$cinemaName} (sur {$totalEmployees} prévus)\n";
        foreach ($positionDistribution as $position => $count) {
            echo "   - {$count} {$position}(s)\n";
        }
    }

    private function calculatePositionDistribution(int $totalEmployees, array $positions): array
    {
        $distribution = [];
        $remaining    = $totalEmployees;

        // D'abord assigner les minimums
        foreach ($positions as $position => $config) {
            $min                     = $config['min'];
            $distribution[$position] = $min;
            $remaining -= $min;
        }

        // Ensuite distribuer le reste selon les ratios
        foreach ($positions as $position => $config) {
            if ($remaining <= 0) {
                break;
            }

            $ratio      = $config['ratio'];
            $additional = max(0, floor($totalEmployees * $ratio) - $distribution[$position]);

            $toAdd = min($additional, $remaining);
            $distribution[$position] += $toAdd;
            $remaining -= $toAdd;
        }

        // Distribuer le reste aux caissiers (poste le plus flexible)
        if ($remaining > 0) {
            $distribution['caissier'] += $remaining;
        }

        return $distribution;
    }

    private function createEmployee(Cinema $cinema, string $position, array $data): void
    {
        // Génération aléatoire du genre
        $isMale = random_int(0, 1) === 1;
        $prenom = $isMale
            ? $data['prenoms']['masculin'][array_rand($data['prenoms']['masculin'])]
            : $data['prenoms']['feminin'][array_rand($data['prenoms']['feminin'])];

        $nom    = $data['noms'][array_rand($data['noms'])];
        $domain = $data['domains'][array_rand($data['domains'])];

        // Email unique avec pattern réaliste
        $emailBase = mb_strtolower(
            $this->removeAccents($prenom).'.'.
            $this->removeAccents($nom).
            random_int(1, 999)
        );
        $email = $emailBase.'@'.$domain;

        // Vérifier l'unicité de l'email
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $emailBase.$counter.'@'.$domain;
            $counter++;
        }

        // Création User
        $user = User::create([
            'uuid'              => Str::uuid()->toString(),
            'email'             => $email,
            'user_type'         => 'employee',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        if (! $user) {
            echo "⚠️  Erreur création utilisateur employé\n";

            return;
        }

        // Création Employee
        $employee = Employee::create([
            'uuid'       => Str::uuid()->toString(),
            'user_id'    => $user->id,
            'cinema_id'  => $cinema->id,
            'first_name' => $prenom,
            'last_name'  => $nom,
            'position'   => $position,
            'is_active'  => true,
        ]);

        // Mettre à jour le profile_id dans la table users pour la relation polymorphe
        $user->profile_id = $employee->id;
        $user->save();

        // Création UserPassword
        DB::table('user_passwords')->insert([
            'user_id'       => $user->id,
            'password_hash' => Hash::make('azerty1234!'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    private function removeAccents(string $str): string
    {
        $accents = [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ç' => 'C', 'ç' => 'c', 'Ñ' => 'N', 'ñ' => 'n',
        ];

        return strtr($str, $accents);
    }
}
