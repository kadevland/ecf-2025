<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seances', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('film_id')->constrained()->onDelete('cascade');
            $table->foreignId('salle_id')->constrained()->onDelete('cascade');

            // Horaires
            $table->datetime('date_heure_debut');
            $table->datetime('date_heure_fin');

            // État de la séance (enum)
            $table->enum('etat', [
                'programmee', 'en_cours', 'terminee', 'annulee', 'complete',
            ])->default('programmee');

            // Gestion des places
            $table->integer('places_disponibles');
            $table->integer('places_reservees')->default(0);

            // Qualité projection (enum - une seule qualité par séance)
            $table->enum('qualite_projection', [
                'standard', '4k', 'imax', '3d', '4dx', 'dolby_atmos', 'screenx', 'ice_immersive',
            ])->default('standard');

            // Tarification
            $table->decimal('prix_base', 8, 2); // Prix de base en euros
            $table->jsonb('tarifs_speciaux')->nullable(); // Tarifs réduits, étudiants, etc.

            // Version et langue
            $table->string('version')->nullable()->default('VF'); // VF, VO, VOST
            $table->boolean('sous_titres')->default(false);
            $table->string('langue_audio')->nullable()->default('français');

            $table->timestamps();

            // Contrainte unique : pas de séances qui se chevauchent dans la même salle
            $table->unique(['salle_id', 'date_heure_debut'], 'unique_salle_horaire');

            // Index pour optimiser les requêtes
            $table->index('date_heure_debut');
            $table->index('etat');
            $table->index('qualite_projection');
            $table->index(['date_heure_debut', 'etat']); // Recherche séances programmées
            $table->index(['film_id', 'date_heure_debut']); // Séances d'un film
            $table->index(['salle_id', 'date_heure_debut']); // Planning d'une salle
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
