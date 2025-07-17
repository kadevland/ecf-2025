<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->uuid('uuid')->unique(); // UUID pour API
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('seance_id')->constrained()->onDelete('cascade');

            // Code cinéma pour génération numéro de réservation
            $table->string('code_cinema', 4); // Ex: "PAR", "LYO", "BRU"

            // Numéro de réservation unique (calculé et stocké)
            $table->string('numero_reservation', 14)->unique(); // Ex: "PAR25D1A2B3C4E"

            // Statut de la réservation (enum)
            $table->enum('statut', [
                'en_attente',    // En attente de paiement
                'confirmee',     // Confirmée mais pas encore payée
                'payee',         // Payée et validée
                'annulee',       // Annulée
                'terminee',      // Terminée (utilisée)
                'expiree',       // Expirée
            ])->default('en_attente');

            // Nombre de places (calculé depuis billets)
            $table->integer('nombre_places');

            // Prix total (calculé depuis billets)
            $table->decimal('prix_total', 8, 2);

            // Dates importantes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Notes optionnelles (raison annulation, etc.)
            $table->text('notes')->nullable();

            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('statut');
            $table->index('code_cinema');
            $table->index('numero_reservation'); // Recherche rapide par numéro
            $table->index(['user_id', 'created_at']); // Historique utilisateur
            $table->index(['seance_id', 'statut']); // Réservations d'une séance
            $table->index('expires_at'); // Pour nettoyage automatique
        });

        // Index fonctionnel PostgreSQL pour recherche par début d'UUID réservation
        // Optimise les recherches par les 4 premiers chars d'UUID dans numéro réservation
        DB::statement('CREATE INDEX idx_reservations_uuid_prefix ON reservations (SUBSTRING(REPLACE(uuid::text, \'-\', \'\'), 1, 4))');

        // TODO: Index sur séance UUID (à faire lors des modifs table seances)
        // DB::statement('CREATE INDEX idx_seances_uuid_prefix ON seances (SUBSTRING(REPLACE(uuid::text, \'-\', \'\'), 1, 4))');
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
