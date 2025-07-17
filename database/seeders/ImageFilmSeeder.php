<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Film;
use App\Models\ImageFilm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ImageFilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createImagesFilm();

        echo "✅ Images de films créées\n";
    }

    private function createImagesFilm(): void
    {
        $films = Film::all();

        if ($films->isEmpty()) {
            echo "⚠️  Aucun film trouvé pour créer des images\n";

            return;
        }

        $imagesCreated = 0;

        foreach ($films as $film) {
            // Créer une affiche principale
            ImageFilm::create([
                'uuid'          => Str::uuid(),
                'film_id'       => $film->id,
                'type'          => 'affiche',
                'url'           => "https://image.tmdb.org/t/p/w500/affiche_{$film->id}.jpg",
                'url_miniature' => "https://image.tmdb.org/t/p/w200/affiche_{$film->id}.jpg",
                'largeur'       => 500,
                'hauteur'       => 750,
                'format'        => 'jpg',
                'principale'    => true,
                'ordre'         => 1,
                'source'        => 'tmdb',
                'source_id'     => "tmdb_{$film->id}",
            ]);

            $imagesCreated++;

            // Créer un backdrop principal
            ImageFilm::create([
                'uuid'          => Str::uuid(),
                'film_id'       => $film->id,
                'type'          => 'backdrop',
                'url'           => "https://image.tmdb.org/t/p/w1280/backdrop_{$film->id}.jpg",
                'url_miniature' => "https://image.tmdb.org/t/p/w300/backdrop_{$film->id}.jpg",
                'largeur'       => 1280,
                'hauteur'       => 720,
                'format'        => 'jpg',
                'principale'    => true,
                'ordre'         => 1,
                'source'        => 'tmdb',
                'source_id'     => "tmdb_backdrop_{$film->id}",
            ]);

            $imagesCreated++;

            // Créer quelques images supplémentaires (2 à 4 par film)
            $nombreImagesSupp = rand(2, 4);

            for ($i = 0; $i < $nombreImagesSupp; $i++) {
                $type      = rand(0, 1) ? 'affiche' : 'backdrop';
                $isAffiche = $type === 'affiche';

                ImageFilm::create([
                    'uuid'          => Str::uuid(),
                    'film_id'       => $film->id,
                    'type'          => $type,
                    'url'           => 'https://image.tmdb.org/t/p/'.($isAffiche ? 'w500' : 'w1280')."/{$type}_{$film->id}_{$i}.jpg",
                    'url_miniature' => 'https://image.tmdb.org/t/p/'.($isAffiche ? 'w200' : 'w300')."/{$type}_{$film->id}_{$i}.jpg",
                    'largeur'       => $isAffiche ? 500 : 1280,
                    'hauteur'       => $isAffiche ? 750 : 720,
                    'format'        => 'jpg',
                    'principale'    => false,
                    'ordre'         => $i + 2,
                    'source'        => 'tmdb',
                    'source_id'     => "tmdb_{$type}_{$film->id}_{$i}",
                ]);

                $imagesCreated++;
            }
        }

        echo "✅ {$imagesCreated} images de films créées\n";
    }
}
