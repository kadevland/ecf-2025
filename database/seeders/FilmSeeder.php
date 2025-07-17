<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Seeder;

final class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = require __DIR__.'/datas/films.php';

        foreach ($films as $filmData) {
            Film::create($filmData);
        }
    }
}
