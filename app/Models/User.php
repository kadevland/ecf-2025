<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\UserStatus;
use App\Domain\Enums\UserType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle Eloquent User compatible avec l'architecture Domain
 *
 * @property string $id
 * @property string $email
 * @property UserType $user_type
 * @property UserStatus $status
 * @property string|null $profile_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'uuid',
        'email',
        'user_type',
        'status',
        'profile_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    // protected $appends = ['password'];

    // Eager loading minimal pour les UUID
    protected $with = ['profile:id,uuid,first_name,last_name'];

    // Relations polymorphiques
    public function profile(): MorphTo
    {
        return $this->morphTo('profile', 'user_type', 'profile_id');
    }

    // Relations
    public function userPassword(): HasOne
    {
        return $this->hasOne(UserPassword::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Authentification Laravel - Password depuis relation
    public function getAuthPassword(): ?string
    {
        return $this->userPassword?->password_hash;
    }

    // Helpers d'authentification
    public function isClient(): bool
    {
        return $this->user_type === UserType::Client;
    }

    public function isEmployee(): bool
    {
        return $this->user_type === UserType::Employee;
    }

    public function isAdmin(): bool
    {
        return $this->user_type === UserType::Administrator;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    // Accesseur pour la compatibilité Laravel Auth en utilisant Attribute
    protected function password(): Attribute
    {
        return new Attribute(
            get: fn () => $this->userPassword?->password_hash
        );
    }

    protected function casts(): array
    {
        return [
            'user_type'         => UserType::class,
            'status'            => UserStatus::class,
            'email_verified_at' => 'datetime',
        ];
    }
}
