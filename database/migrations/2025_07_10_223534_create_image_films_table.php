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
        Schema::create('image_films', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('film_id')->constrained()->onDelete('cascade');

            // Informations image
            $table->string('type'); // 'affiche', 'backdrop', 'still', 'logo'
            $table->string('url');
            $table->string('url_miniature')->nullable();
            $table->integer('largeur')->nullable();
            $table->integer('hauteur')->nullable();
            $table->string('format')->nullable(); // 'jpg', 'png', 'webp'

            // Métadonnées
            $table->boolean('principale')->default(false);
            $table->integer('ordre')->default(0);
            $table->string('source')->nullable(); // 'tmdb', 'manuel', 'distributeur'
            $table->string('source_id')->nullable(); // ID externe (TMDB)

            $table->timestamps();

            // Index
            $table->index(['film_id', 'type']);
            $table->index(['film_id', 'principale']);
            $table->index('ordre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_films');
    }
};
