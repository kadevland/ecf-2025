<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Eloquent RevueFilm
 *
 * @property int $id
 * @property string $uuid
 * @property int $film_id
 * @property int $user_id
 * @property int $note
 * @property string|null $commentaire
 * @property string|null $titre_avis
 * @property bool $spoiler
 * @property bool $approuve
 * @property bool $signale
 * @property string|null $motif_signalement
 * @property int $likes
 * @property int $dislikes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class RevueFilm extends Model
{
    use HasFactory;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'film_id',
        'user_id',
        'note',
        'commentaire',
        'titre_avis',
        'spoiler',
        'approuve',
        'signale',
        'motif_signalement',
        'likes',
        'dislikes',
    ];

    // Relations
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes helper
    public function isApprouve(): bool
    {
        return $this->approuve;
    }

    public function hasSpoiler(): bool
    {
        return $this->spoiler;
    }

    public function isSignale(): bool
    {
        return $this->signale;
    }

    public function getNoteFormatee(): string
    {
        return $this->note.'/10';
    }

    public function getReactionScore(): int
    {
        return $this->likes - $this->dislikes;
    }

    public function incrementLikes(): void
    {
        $this->increment('likes');
    }

    public function incrementDislikes(): void
    {
        $this->increment('dislikes');
    }

    protected function casts(): array
    {
        return [
            'note'     => 'integer',
            'spoiler'  => 'boolean',
            'approuve' => 'boolean',
            'signale'  => 'boolean',
            'likes'    => 'integer',
            'dislikes' => 'integer',
        ];
    }
}
