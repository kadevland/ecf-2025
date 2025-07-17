<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use Illuminate\Database\Seeder;

final class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cinemas = require __DIR__.'/datas/cinemas.php';

        foreach ($cinemas as $cinemaData) {
            Cinema::create($cinemaData);
        }
    }
}
