<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer les contraintes existantes
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_type_check');
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_priorite_check');
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_statut_check');

        // Ajouter les nouvelles contraintes avec les valeurs correctes
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_type_check CHECK (type IN ('projection', 'audio', 'eclairage', 'climatisation', 'securite', 'nettoyage', 'equipement', 'siege', 'autre'))");
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_priorite_check CHECK (priorite IN ('faible', 'normale', 'elevee', 'critique'))");
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_statut_check CHECK (statut IN ('ouvert', 'en_cours', 'resolu', 'ferme', 'reporte'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vider la table pour éviter les violations de contraintes
        DB::table('incidents')->truncate();

        // Restaurer les contraintes originales
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_type_check');
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_priorite_check');
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT IF EXISTS incidents_statut_check');

        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_type_check CHECK (type IN ('technique', 'securite', 'proprete', 'autre'))");
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_priorite_check CHECK (priorite IN ('basse', 'normale', 'haute', 'critique'))");
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_statut_check CHECK (statut IN ('ouvert', 'en_cours', 'resolu', 'ferme'))");
    }
};
