<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\PrioriteIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\TypeIncident;
use App\Models\Traits\HasSqids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Eloquent Incident
 *
 * @property string $id
 * @property string $uuid
 * @property string $titre
 * @property string $description
 * @property TypeIncident $type
 * @property PrioriteIncident $priorite
 * @property StatutIncident $statut
 * @property string $localisation
 * @property string $rapporte_par_id
 * @property string $cinema_id
 * @property string|null $salle_id
 * @property string|null $assigne_a_id
 * @property string|null $solution_apportee
 * @property string|null $commentaires_internes
 * @property \Carbon\CarbonImmutable|null $resolue_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Incident extends Model
{
    use HasFactory, HasSqids;

    protected $fillable = [
        'uuid',
        'titre',
        'description',
        'type',
        'priorite',
        'statut',
        'localisation',
        'rapporte_par_id',
        'cinema_id',
        'salle_id',
        'assigne_a_id',
        'solution_apportee',
        'commentaires_internes',
        'resolue_at',
    ];

    // Relations
    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function rapportePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rapporte_par_id');
    }

    public function assigneA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a_id');
    }

    // Méthodes helper
    public function estOuvert(): bool
    {
        return $this->statut === StatutIncident::Ouvert;
    }

    public function estEnCours(): bool
    {
        return $this->statut === StatutIncident::EnCours;
    }

    public function estResolu(): bool
    {
        return $this->statut === StatutIncident::Resolu;
    }

    public function estFerme(): bool
    {
        return $this->statut === StatutIncident::Ferme;
    }

    public function estActif(): bool
    {
        return in_array($this->statut, [StatutIncident::Ouvert, StatutIncident::EnCours]);
    }

    public function estAssigne(): bool
    {
        return $this->assigne_a_id !== null;
    }

    public function estCritique(): bool
    {
        return $this->priorite === PrioriteIncident::Critique;
    }

    public function estHaute(): bool
    {
        return $this->priorite === PrioriteIncident::Haute;
    }

    protected function casts(): array
    {
        return [
            'type'       => TypeIncident::class,
            'priorite'   => PrioriteIncident::class,
            'statut'     => StatutIncident::class,
            'resolue_at' => 'immutable_datetime',
        ];
    }
}
