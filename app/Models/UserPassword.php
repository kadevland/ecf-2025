<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pour la table user_passwords
 *
 * @property string $user_id
 * @property string $password_hash
 * @property int $failed_attempts
 * @property \Illuminate\Support\Carbon|null $locked_until
 * @property \Illuminate\Support\Carbon|null $last_attempt_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class UserPassword extends Model
{
    protected $fillable = [
        'user_id',
        'password_hash',
        'failed_attempts',
        'locked_until',
        'last_attempt_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_attempt_at' => now(),
        ]);
    }

    public function incrementFailedAttempts(): void
    {
        $attempts    = $this->failed_attempts + 1;
        $lockedUntil = null;

        // Verrouillage après 5 tentatives échouées
        if ($attempts >= 5) {
            $lockedUntil = now()->addMinutes(15);
        }

        $this->update([
            'failed_attempts' => $attempts,
            'locked_until'    => $lockedUntil,
            'last_attempt_at' => now(),
        ]);
    }

    protected function casts(): array
    {
        return [
            'failed_attempts' => 'integer',
            'locked_until'    => 'datetime',
            'last_attempt_at' => 'datetime',
        ];
    }
}
