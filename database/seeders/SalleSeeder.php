<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Salle;
use Illuminate\Database\Seeder;

final class SalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salles = require __DIR__.'/datas/salles.php';

        foreach ($salles as $salleData) {
            // Récupérer le cinema_id à partir de l'UUID
            $cinema = Cinema::where('uuid', $salleData['cinema_uuid'])->first();

            if (! $cinema) {
                continue; // Skip si le cinéma n'existe pas
            }

            // Remplacer cinema_uuid par cinema_id
            $salleData['cinema_id'] = $cinema->id;
            unset($salleData['cinema_uuid']);

            Salle::create($salleData);
        }
    }
}
