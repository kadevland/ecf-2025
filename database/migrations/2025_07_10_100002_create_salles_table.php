<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salles', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('cinema_id')->constrained()->onDelete('cascade');
            $table->string('numero'); // Numéro de salle (ex: "1", "A", "Premium 1")
            $table->string('nom')->nullable(); // Nom optionnel (ex: "Salle IMAX")
            $table->integer('capacite');

            // État de la salle (enum)
            $table->enum('etat', ['active', 'maintenance', 'hors_service', 'en_renovation', 'fermee'])->default('active');

            // Qualités projection (JSONB pour plusieurs choix)
            $table->jsonb('qualites_projection')->nullable(); // ['standard', '4k', 'imax', ...]

            // Équipements et configuration (JSON)
            $table->jsonb('equipements')->nullable(); // ['3D', 'IMAX', 'Dolby Atmos', ...]
            $table->jsonb('plan_salle')->nullable(); // Configuration des sièges

            $table->timestamps();

            // Contraintes
            $table->unique(['cinema_id', 'numero']); // Numéro unique par cinéma

            // Index
            $table->index('etat');
            $table->index('capacite');
            // Index GIN pour recherche dans JSONB
            $table->index('qualites_projection', 'idx_salles_qualites_projection')->using('gin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};
