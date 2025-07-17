<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Reservation::class,
            User::class,
            'id',           // Clé étrangère sur la table intermédiaire (users) - clients.user_id pointe vers users.id
            'user_id',      // Clé étrangère sur la table finale (reservations) - reservations.user_id pointe vers users.id
            'user_id',      // Clé locale sur la table parent (clients) - clients.user_id
            'id'            // Clé locale sur la table intermédiaire (users) - users.id
        );
    }
}
