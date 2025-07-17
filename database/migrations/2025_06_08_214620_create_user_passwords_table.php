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
        Schema::create('user_passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('password_hash');
            $table->string('password_reset_token')
                ->nullable()
                ->index();
            $table->timestamp('password_reset_expires')
                ->nullable();
            $table->boolean('must_change_password')
                ->default(false)
                ->index();
            $table->timestamp('last_password_change')
                ->nullable();
            $table->integer('failed_login_attempts')
                ->default(0)
                ->index(); // Pour requêtes sécurité
            $table->timestamp('locked_until')
                ->nullable()
                ->index(); // Performance vérification lock
            $table->timestamps();

            // Index unique user_id (un seul password par user)
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new Exception('CRITICAL: Cannot drop user_passwords table in production! This would expose security breach!');
        }
        Schema::dropIfExists('user_passwords');
    }
};
