<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billets', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('seance_id')->constrained()->onDelete('cascade');

            // Identifiant du billet
            $table->string('numero_billet')->unique(); // Ex: "BIL-2025-001234-01"

            // Place et tarif
            $table->string('place'); // Ex: "A12", "Balcon-05"
            $table->enum('type_tarif', [
                'plein', 'reduit', 'etudiant', 'senior', 'enfant', 'groupe',
            ])->default('plein');
            $table->decimal('prix', 8, 2);

            // QR Code pour validation
            $table->string('qr_code')->nullable()->unique();

            // Utilisation du billet
            $table->boolean('utilise')->default(false);
            $table->datetime('date_utilisation')->nullable();

            $table->timestamps();

            // Contraintes

            // Contrainte unique : une place ne peut être occupée qu'une fois par séance
            $table->unique(['seance_id', 'place'], 'unique_place_seance');

            // Index pour optimiser les requêtes
            $table->index('numero_billet');
            $table->index('qr_code');
            $table->index('utilise');
            $table->index(['seance_id', 'utilise']); // Billets utilisés d'une séance
            $table->index(['reservation_id', 'place']); // Billets d'une réservation
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billets');
    }
};
