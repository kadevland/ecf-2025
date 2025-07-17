<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use App\Models\Traits\HasSqids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Eloquent Seance
 *
 * @property int $id
 * @property string $film_id
 * @property string $salle_id
 * @property \Illuminate\Support\Carbon $date_heure_debut
 * @property \Illuminate\Support\Carbon $date_heure_fin
 * @property EtatSeance $etat
 * @property int $places_disponibles
 * @property int $places_reservees
 * @property QualiteProjection $qualite_projection
 * @property float $prix_base
 * @property array|null $tarifs_speciaux
 * @property string|null $version
 * @property bool $sous_titres
 * @property string|null $langue_audio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Seance extends Model
{
    use HasFactory, HasSqids;

    // Utilise l'ID auto-increment par défaut de Laravel
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'uuid',
        'film_id',
        'salle_id',
        'date_heure_debut',
        'date_heure_fin',
        'etat',
        'places_disponibles',
        'places_reservees',
        'qualite_projection',
        'prix_base',
        'tarifs_speciaux',
        'version',
        'sous_titres',
        'langue_audio',
    ];

    // Relations
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function billets(): HasMany
    {
        return $this->hasMany(Billet::class);
    }

    // Méthodes helper
    public function isActive(): bool
    {
        return $this->etat === EtatSeance::Programmee;
    }

    public function isComplete(): bool
    {
        // TODO: Ajouter EtatSeance::Complete dans l'enum si nécessaire
        return false; // $this->etat === EtatSeance::Complete;
    }

    public function isAnnulee(): bool
    {
        return $this->etat === EtatSeance::Annulee;
    }

    public function hasAvailableSeats(): bool
    {
        return $this->places_disponibles > 0;
    }

    public function isFull(): bool
    {
        return $this->places_disponibles === 0;
    }

    public function getHeureDebut(): string
    {
        return $this->date_heure_debut->format('H:i');
    }

    public function getHeureFin(): string
    {
        return $this->date_heure_fin->format('H:i');
    }

    public function getDate(): string
    {
        return $this->date_heure_debut->format('d/m/Y');
    }

    public function getJour(): string
    {
        return $this->date_heure_debut->format('l j F Y');
    }

    public function getDuree(): int
    {
        return $this->date_heure_debut->diffInMinutes($this->date_heure_fin);
    }

    public function getPrixFormate(): string
    {
        return number_format($this->prix_base, 2).' €';
    }

    public function getVersionComplete(): string
    {
        $version = $this->version ?? 'VF';

        if ($this->sous_titres) {
            $version .= ' sous-titrée';
        }

        if ($this->langue_audio && $this->langue_audio !== 'français') {
            $version .= ' ('.$this->langue_audio.')';
        }

        return $version;
    }

    public function getTauxOccupation(): float
    {
        $capacite = $this->salle->capacite ?? 0;

        if ($capacite === 0) {
            return 0;
        }

        return ($this->places_reservees / $capacite) * 100;
    }

    protected function casts(): array
    {
        return [
            'date_heure_debut'    => 'datetime',
            'date_heure_fin'      => 'datetime',
            'etat'                => EtatSeance::class,
            'qualite_projection'  => QualiteProjection::class,
            'places_disponibles'  => 'integer',
            'places_reservees'    => 'integer',
            'prix_base'           => 'float',
            'tarifs_speciaux'     => 'array',
            'sous_titres'         => 'boolean',
        ];
    }
}
