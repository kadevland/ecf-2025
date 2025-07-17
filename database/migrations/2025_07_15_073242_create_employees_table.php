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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')
                ->unique(); // Identifiant entité
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('cinema_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('position'); // 'caissier', 'projectionniste', 'manager', 'maintenance'
            $table->boolean('is_active')
                ->default(true)
                ->index();
            $table->timestamps();

            // Index unique user_id
            $table->unique('user_id');
            // Index pour recherches
            $table->index(['first_name', 'last_name']);
            $table->index(['cinema_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new Exception('Cannot drop employees table in production! Employee data loss!');
        }
        Schema::dropIfExists('employees');
    }
};
