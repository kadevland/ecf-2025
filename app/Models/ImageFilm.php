<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Eloquent ImageFilm
 *
 * @property int $id
 * @property string $uuid
 * @property int $film_id
 * @property string $type
 * @property string $url
 * @property string|null $url_miniature
 * @property int|null $largeur
 * @property int|null $hauteur
 * @property string|null $format
 * @property bool $principale
 * @property int $ordre
 * @property string|null $source
 * @property string|null $source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class ImageFilm extends Model
{
    use HasFactory;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'film_id',
        'type',
        'url',
        'url_miniature',
        'largeur',
        'hauteur',
        'format',
        'principale',
        'ordre',
        'source',
        'source_id',
    ];

    // Relations
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    // Méthodes helper
    public function isPrincipale(): bool
    {
        return $this->principale;
    }

    public function isAffiche(): bool
    {
        return $this->type === 'affiche';
    }

    public function isBackdrop(): bool
    {
        return $this->type === 'backdrop';
    }

    public function getDimensions(): string
    {
        if ($this->largeur && $this->hauteur) {
            return "{$this->largeur}x{$this->hauteur}";
        }

        return 'Dimensions inconnues';
    }

    public function getAspectRatio(): ?float
    {
        if ($this->largeur && $this->hauteur && $this->hauteur > 0) {
            return round($this->largeur / $this->hauteur, 2);
        }

        return null;
    }

    protected function casts(): array
    {
        return [
            'principale' => 'boolean',
            'largeur'    => 'integer',
            'hauteur'    => 'integer',
            'ordre'      => 'integer',
        ];
    }
}
