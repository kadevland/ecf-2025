<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Informations de base
            $table->string('titre');
            $table->text('description');

            // Enums
            $table->enum('type', ['technique', 'securite', 'proprete', 'autre']);
            $table->enum('priorite', ['basse', 'normale', 'haute', 'critique']);
            $table->enum('statut', ['ouvert', 'en_cours', 'resolu', 'ferme'])->default('ouvert');

            // Localisation
            $table->string('localisation');

            // Relations
            $table->foreignId('rapporte_par_id')->constrained('users');
            $table->foreignId('cinema_id')->constrained('cinemas');
            $table->foreignId('salle_id')->nullable()->constrained('salles');
            $table->foreignId('assigne_a_id')->nullable()->constrained('users');

            // Résolution
            $table->text('solution_apportee')->nullable();
            $table->text('commentaires_internes')->nullable();
            $table->timestamp('resolue_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('statut');
            $table->index('priorite');
            $table->index('type');
            $table->index('cinema_id');
            $table->index('rapporte_par_id');
            $table->index('assigne_a_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
