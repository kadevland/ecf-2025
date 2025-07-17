<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('duree_minutes');

            // Catégorie (enum)
            $table->enum('categorie', [
                'action', 'aventure', 'comedie', 'drame', 'fantastique',
                'horreur', 'romance', 'science_fiction', 'thriller',
                'animation', 'documentaire', 'musical', 'western',
                'historique', 'policier',
            ])->nullable();

            // Équipe
            $table->string('realisateur')->nullable();
            $table->jsonb('acteurs')->nullable(); // Array des acteurs principaux
            $table->string('pays_origine')->nullable();

            // Dates et médias
            $table->date('date_sortie')->nullable();
            $table->string('bande_annonce_url')->nullable();
            $table->string('affiche_url')->nullable();

            // TMDB integration
            $table->integer('tmdb_id')->nullable()->unique();

            // Qualités projection disponibles (JSONB pour plusieurs choix)
            $table->jsonb('qualites_projection')->nullable(); // ['standard', '4k', 'imax', ...]

            // Notes et avis
            $table->decimal('note_moyenne', 3, 1)->nullable(); // Ex: 8.5
            $table->integer('nombre_votes')->default(0);

            $table->timestamps();

            // Index
            $table->index('titre');
            $table->index('categorie');
            $table->index('date_sortie');
            $table->index('note_moyenne');
            $table->index('tmdb_id');
            // Index GIN pour recherche dans JSONB
            $table->index('qualites_projection', 'idx_films_qualites_projection')->using('gin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
