<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Film;
use App\Models\RevueFilm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class RevueFilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createRevuesFilm();

        echo "✅ Revues de films créées\n";
    }

    private function createRevuesFilm(): void
    {
        $films = Film::all();
        $users = User::whereIn('user_type', ['client'])->get();

        if ($films->isEmpty() || $users->isEmpty()) {
            echo "⚠️  Aucun film ou utilisateur trouvé pour créer des revues\n";

            return;
        }

        $commentaires = [
            'Excellent film, je recommande vivement !',
            'Un peu déçu par le scénario mais les effets visuels sont superbes.',
            'Une œuvre magistrale qui mérite tous les éloges.',
            'Film correct sans plus, divertissant mais pas mémorable.',
            'Complètement raté, j\'ai quitté la salle au bout d\'une heure.',
            'Très bon divertissement familial, parfait pour le weekend.',
            'Trop long et prévisible, dommage car l\'idée était bonne.',
            'Chef-d\'œuvre cinématographique, à voir absolument !',
            'Bof bof, j\'ai vu mieux dans le genre.',
            'Surprise totale ! Je ne m\'attendais pas à un film si réussi.',
        ];

        $titres = [
            'Un must-see absolu',
            'Pas terrible',
            'Excellente soirée',
            'Déception totale',
            'Très bon moment',
            'À éviter',
            'Parfait pour se détendre',
            'Œuvre d\'art',
            'Moyen',
            'Agréable surprise',
        ];

        $revuesCreated = 0;

        foreach ($films as $film) {
            // Créer entre 2 et 8 revues par film
            $nombreRevues = rand(2, 8);
            $usersUsed    = [];

            for ($i = 0; $i < $nombreRevues; $i++) {
                // Éviter les doublons d'utilisateurs pour un même film
                $availableUsers = $users->whereNotIn('id', $usersUsed);
                if ($availableUsers->isEmpty()) {
                    break;
                }

                $user        = $availableUsers->random();
                $usersUsed[] = $user->id;

                $note       = rand(1, 10);
                $hasSpoiler = rand(0, 100) < 15; // 15% de chance de spoiler
                $isApprouve = rand(0, 100) < 90; // 90% de chance d'être approuvé
                $isSignale  = rand(0, 100) < 5; // 5% de chance d'être signalé

                RevueFilm::create([
                    'uuid'              => Str::uuid(),
                    'film_id'           => $film->id,
                    'user_id'           => $user->id,
                    'note'              => $note,
                    'commentaire'       => $commentaires[array_rand($commentaires)],
                    'titre_avis'        => $titres[array_rand($titres)],
                    'spoiler'           => $hasSpoiler,
                    'approuve'          => $isApprouve,
                    'signale'           => $isSignale,
                    'motif_signalement' => $isSignale ? 'Contenu inapproprié' : null,
                    'likes'             => rand(0, 50),
                    'dislikes'          => rand(0, 20),
                ]);

                $revuesCreated++;
            }
        }

        echo "✅ {$revuesCreated} revues de films créées\n";
    }
}
