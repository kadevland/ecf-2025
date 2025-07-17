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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')
                ->unique(); // Identifiant entité
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20)
                ->nullable();
            $table->date('birth_date')
                ->nullable();
            $table->timestamps();

            // Index unique user_id
            $table->unique('user_id');
            // Index nom/prénom pour recherches
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new Exception('Cannot drop clients table in production! Customer data loss!');
        }
        Schema::dropIfExists('clients');
    }
};
