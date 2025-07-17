<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'cinema_id',
        'first_name',
        'last_name',
        'phone',
        'position',
        'hire_date',
        'salary',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary'    => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }
}
