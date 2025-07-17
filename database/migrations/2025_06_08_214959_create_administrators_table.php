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
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')
                ->unique(); // Identifiant entité
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_active')
                ->default(true)
                ->index();
            $table->timestamps();

            // Index unique user_id
            $table->unique('user_id');
            // Index pour recherches
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new Exception('Cannot drop administrators table in production! Admin access loss!');
        }
        Schema::dropIfExists('administrators');
    }
};
