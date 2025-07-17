<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require __DIR__.'/datas/clients.php';

        foreach ($data['cinema_configs'] as $cinemaUuid => $config) {
            $this->createClientsForCinema($cinemaUuid, $config, $data);
        }
    }

    private function createClientsForCinema(string $cinemaUuid, array $config, array $data): void
    {
        $clientsCount = $config['clients'];
        $cinemaName   = $config['name'];

        for ($i = 1; $i <= $clientsCount; $i++) {
            // Génération aléatoire du genre
            $isMale = random_int(0, 1) === 1;
            $prenom = $isMale
                ? $data['prenoms']['masculin'][array_rand($data['prenoms']['masculin'])]
                : $data['prenoms']['feminin'][array_rand($data['prenoms']['feminin'])];

            $nom    = $data['noms'][array_rand($data['noms'])];
            $ville  = $data['villes'][array_rand($data['villes'])];
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
            try {
                $user = User::create([
                    'uuid'              => Str::uuid()->toString(),
                    'email'             => $email,
                    'user_type'         => 'client',
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]);

                if (! $user) {
                    echo "⚠️  Erreur création utilisateur pour {$prenom} {$nom} - User null\n";

                    continue;
                }

                // Refresh pour s'assurer que l'ID est bien défini
                $user->refresh();
            } catch (Exception $e) {
                echo "⚠️  Erreur création utilisateur pour {$prenom} {$nom}: ".$e->getMessage()."\n";

                continue;
            }

            // Création Client
            $client = Client::create([
                'uuid'       => Str::uuid()->toString(),
                'user_id'    => $user->id,
                'first_name' => $prenom,
                'last_name'  => $nom,
                'phone'      => $this->generatePhoneNumber(),
                'birth_date' => $this->generateBirthDate(),
            ]);

            // Mettre à jour le profile_id dans la table users pour la relation polymorphe
            $user->profile_id = $client->id;
            $user->save();

            // Création UserPassword
            try {
                DB::table('user_passwords')->insert([
                    'user_id'       => $user->id,
                    'password_hash' => Hash::make('azerty1234!'),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            } catch (Exception $e) {
                echo "⚠️  Erreur création mot de passe pour user ID {$user->id}: ".$e->getMessage()."\n";

                continue;
            }
        }

        echo "✅ {$clientsCount} clients créés pour {$cinemaName}\n";
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

    private function generatePhoneNumber(): string
    {
        $prefixes = ['06', '07', '01', '02', '03', '04', '05'];
        $prefix   = $prefixes[array_rand($prefixes)];

        $number = $prefix;
        for ($i = 0; $i < 8; $i++) {
            $number .= random_int(0, 9);
        }

        return $number;
    }

    private function generateBirthDate(): string
    {
        $minAge = 18;
        $maxAge = 75;
        $age    = random_int($minAge, $maxAge);

        $birthYear  = date('Y') - $age;
        $birthMonth = random_int(1, 12);
        $birthDay   = random_int(1, 28); // Évite les problèmes de dates

        return sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
    }
}
