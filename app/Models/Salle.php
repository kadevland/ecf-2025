<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\QualitesProjectionCast;
use App\Domain\Enums\EtatSalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Eloquent Salle
 *
 * @property string $id
 * @property string $cinema_id
 * @property string $numero
 * @property string|null $nom
 * @property int $capacite
 * @property EtatSalle $etat
 * @property array|null $qualites_projection
 * @property array|null $equipements
 * @property array|null $plan_salle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Salle extends Model
{
    use HasFactory;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'cinema_id',
        'numero',
        'nom',
        'capacite',
        'etat',
        'qualites_projection',
        'equipements',
        'plan_salle',
    ];

    // Relations
    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    // Méthodes helper
    public function isActive(): bool
    {
        return $this->etat === EtatSalle::Active;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->etat === EtatSalle::Maintenance;
    }

    public function isOutOfOrder(): bool
    {
        return $this->etat === EtatSalle::HorsService;
    }

    public function hasEquipement(string $equipement): bool
    {
        return in_array($equipement, $this->equipements ?? []);
    }

    public function is3D(): bool
    {
        return $this->hasEquipement('3D');
    }

    public function isIMAX(): bool
    {
        return $this->hasEquipement('IMAX');
    }

    public function isDolbyAtmos(): bool
    {
        return $this->hasEquipement('Dolby Atmos');
    }

    public function getIdentifiant(): string
    {
        return $this->nom ? "{$this->numero} - {$this->nom}" : $this->numero;
    }

    public function getCapaciteFormatee(): string
    {
        return $this->capacite.' places';
    }

    public function getQualiteProjectionLabel(): string
    {
        return $this->qualite_projection?->value ?? 'Standard';
    }

    protected function casts(): array
    {
        return [
            'etat'                 => EtatSalle::class,
            'qualites_projection'  => QualitesProjectionCast::class,
            'equipements'          => 'array',
            'plan_salle'           => 'array',
            'capacite'             => 'integer',
        ];
    }
}
