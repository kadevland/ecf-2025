<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->string('code_cinema', 4)->unique();
            $table->string('nom');
            $table->text('description')->nullable();

            // Adresse (JSON)
            $table->jsonb('adresse'); // {rue, code_postal, ville, pays}
            $table->jsonb('coordonnees_gps')->nullable(); // {latitude, longitude}

            // Contact
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // Statut (enum)
            $table->enum('statut', ['actif', 'ferme', 'en_renovation', 'en_maintenance'])->default('actif');

            // Horaires et services (JSON)
            $table->jsonb('horaires_ouverture')->nullable();
            $table->jsonb('services')->nullable(); // [parking, restaurant, boutique, ...]

            $table->timestamps();

            // Index
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
};
