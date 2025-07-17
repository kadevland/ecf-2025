<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revue_films', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('film_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Note et avis
            $table->integer('note'); // 1 à 10
            $table->text('commentaire')->nullable();
            $table->string('titre_avis')->nullable();

            // Métadonnées
            $table->boolean('spoiler')->default(false);
            $table->boolean('approuve')->default(false);
            $table->boolean('signale')->default(false);
            $table->text('motif_signalement')->nullable();

            // Interactions
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);

            $table->timestamps();

            // Contraintes

            // Un utilisateur ne peut noter qu'une fois le même film
            $table->unique(['film_id', 'user_id']);

            // Index
            $table->index(['film_id', 'approuve']);
            $table->index('note');
            $table->index(['user_id', 'created_at']);
            $table->index('signale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revue_films');
    }
};
