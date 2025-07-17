<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSqids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Administrator extends Model
{
    use HasSqids;

    protected $fillable = [
        'uuid',
        'user_id',
        'first_name',
        'last_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nom complet
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Vérifier si l'administrateur est actif
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
