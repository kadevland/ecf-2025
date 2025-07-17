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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')
                ->unique(); // Identifiant entité
            $table->string('email', 320)
                ->unique();
            $table->string('user_type')
                ->index(); // 'client', 'administrator'
            $table->string('status')
                ->default('active')
                ->index(); // 'active', 'suspended', 'deleted'
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new Exception('Cannot drop users table in production!');
        }
        Schema::dropIfExists('users');
    }
};
