<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Eloquent Reservation
 *
 * @property string $id
 * @property string $user_id
 * @property string $seance_id
 * @property string $numero_reservation
 * @property string $statut
 * @property int $nombre_places
 * @property array $places_selectionnees
 * @property float $prix_total
 * @property float|null $montant_paye
 * @property string|null $methode_paiement
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $date_paiement
 * @property \Illuminate\Support\Carbon|null $date_annulation
 * @property string|null $motif_annulation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Reservation extends Model
{
    use HasFactory;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'user_id',
        'seance_id',
        'code_cinema',
        'numero_reservation',
        'statut',
        'nombre_places',
        'places_selectionnees',
        'prix_total',
        'confirmed_at',
        'expires_at',
        'notes',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    public function billets(): HasMany
    {
        return $this->hasMany(Billet::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'user_id', 'user_id');
    }

    // Méthodes helper pour les statuts
    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function isConfirmee(): bool
    {
        return $this->statut === 'confirmee';
    }

    public function isPayee(): bool
    {
        return $this->statut === 'payee';
    }

    public function isAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function isUtilisee(): bool
    {
        return $this->statut === 'utilisee';
    }

    public function canBeCancelled(): bool
    {
        if ($this->isAnnulee() || $this->isUtilisee()) {
            return false;
        }

        // Vérifier si la séance n'a pas déjà commencé
        return $this->seance->date_heure_debut > now();
    }

    public function getMontantTotalFormate(): string
    {
        return number_format($this->prix_total, 2).' €';
    }

    public function getMontantPayeFormate(): string
    {
        return number_format($this->montant_paye ?? 0, 2).' €';
    }

    public function getPlacesFormatees(): string
    {
        if (empty($this->places_selectionnees)) {
            return $this->nombre_places.' place(s)';
        }

        return implode(', ', $this->places_selectionnees);
    }

    public function getStatutLabel(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'confirmee'  => 'Confirmée',
            'payee'      => 'Payée',
            'annulee'    => 'Annulée',
            'utilisee'   => 'Utilisée',
            default      => 'Inconnu'
        };
    }

    public function getStatutBadgeClass(): string
    {
        return match ($this->statut) {
            'en_attente' => 'badge-warning',
            'confirmee'  => 'badge-info',
            'payee'      => 'badge-success',
            'annulee'    => 'badge-error',
            'utilisee'   => 'badge-neutral',
            default      => 'badge-ghost'
        };
    }

    public function getTempsRestantAvantSeance(): ?string
    {
        if (! $this->seance) {
            return null;
        }

        $maintenant = now();
        $debut      = $this->seance->date_heure_debut;

        if ($debut <= $maintenant) {
            return null; // Séance déjà commencée
        }

        $diff = $debut->diff($maintenant);

        if ($diff->days > 0) {
            return $diff->days.' jour(s)';
        }

        if ($diff->h > 0) {
            return $diff->h.'h'.sprintf('%02d', $diff->i);
        }

        return $diff->i.' minute(s)';
    }

    protected function casts(): array
    {
        return [
            'places_selectionnees' => 'array',
            'prix_total'           => 'float',
            'montant_paye'         => 'float',
            'nombre_places'        => 'integer',
            'date_paiement'        => 'datetime',
            'date_annulation'      => 'datetime',
        ];
    }
}
