<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\QualitesProjectionCast;
use App\Domain\Enums\CategorieFilm;
use App\Models\Traits\HasSqids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Eloquent Film
 *
 * @property string $id
 * @property string $titre
 * @property string|null $description
 * @property int $duree_minutes
 * @property CategorieFilm|null $categorie
 * @property string|null $realisateur
 * @property array|null $acteurs
 * @property array|null $qualites_projection
 * @property string|null $pays_origine
 * @property \Illuminate\Support\Carbon|null $date_sortie
 * @property string|null $bande_annonce_url
 * @property string|null $affiche_url
 * @property int|null $tmdb_id
 * @property float|null $note_moyenne
 * @property int $nombre_votes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Film extends Model
{
    use HasFactory, HasSqids;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'titre',
        'description',
        'duree_minutes',
        'categorie',
        'realisateur',
        'acteurs',
        'qualites_projection',
        'pays_origine',
        'date_sortie',
        'bande_annonce_url',
        'affiche_url',
        'tmdb_id',
        'note_moyenne',
        'nombre_votes',
    ];

    // Relations
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImageFilm::class);
    }

    public function revues(): HasMany
    {
        return $this->hasMany(RevueFilm::class);
    }

    // Méthodes helper
    public function getDureeFormatee(): string
    {
        $heures  = intdiv($this->duree_minutes, 60);
        $minutes = $this->duree_minutes % 60;

        if ($heures > 0) {
            return sprintf('%dh%02d', $heures, $minutes);
        }

        return sprintf('%d min', $minutes);
    }

    public function hasTrailer(): bool
    {
        return ! empty($this->bande_annonce_url);
    }

    public function hasPoster(): bool
    {
        return ! empty($this->affiche_url);
    }

    public function isFromTMDB(): bool
    {
        return $this->tmdb_id !== null;
    }

    public function getNoteMoyenneFormatee(): string
    {
        if ($this->note_moyenne === null) {
            return 'Non noté';
        }

        return number_format($this->note_moyenne, 1).'/10';
    }

    public function getPosterUrl(): string
    {
        // Si l'URL est invalide (example.com), utiliser une image par défaut
        if (! $this->affiche_url || str_contains($this->affiche_url, 'images.example.com')) {
            return 'https://picsum.photos/300/450?random='.$this->id;
        }

        return $this->affiche_url;
    }

    protected function casts(): array
    {
        return [
            'categorie'           => CategorieFilm::class,
            'acteurs'             => 'array',
            'qualites_projection' => QualitesProjectionCast::class,
            'date_sortie'         => 'date',
            'note_moyenne'        => 'float',
            'nombre_votes'        => 'integer',
            'duree_minutes'       => 'integer',
            'tmdb_id'             => 'integer',
        ];
    }
}
