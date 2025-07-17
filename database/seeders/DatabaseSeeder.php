<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders dans l'ordre des dépendances
        $this->call([
            // 1. Infrastructure de base
            CinemaSeeder::class,
            SalleSeeder::class,

            // 2. Contenu principal
            FilmSeeder::class,
            ImageFilmSeeder::class,  // Images après les films

            // 3. Utilisateurs et authentification
            ClientSeeder::class,
            EmployeeSeeder::class,
            AdminSeeder::class,

            // 4. Programmation et séances
            SeanceSeeder::class,

            // 5. Réservations et billets
            ReservationSeeder::class,  // Crée aussi les billets

            // 6. Contenu utilisateur
            RevueFilmSeeder::class,    // Critiques après les utilisateurs

            // 7. Incidents (après employés)
            IncidentSeeder::class,

            // 8. Données de démonstration
            ClientDemoSeeder::class,
        ]);
    }
}
