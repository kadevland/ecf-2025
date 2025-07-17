<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Eloquent Billet
 *
 * @property string $id
 * @property string $reservation_id
 * @property string $seance_id
 * @property string $numero_billet
 * @property string $place
 * @property string $type_tarif
 * @property float $prix
 * @property string|null $qr_code
 * @property bool $utilise
 * @property \Illuminate\Support\Carbon|null $date_utilisation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Billet extends Model
{
    use HasFactory;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'reservation_id',
        'seance_id',
        'numero_billet',
        'place',
        'type_tarif',
        'prix',
        'qr_code',
        'utilise',
        'date_utilisation',
    ];

    // Relations
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    // Méthodes helper
    public function isUtilise(): bool
    {
        return $this->utilise;
    }

    public function canBeUsed(): bool
    {
        if ($this->isUtilise()) {
            return false;
        }

        // Vérifier si la séance n'est pas terminée
        return $this->seance->date_heure_fin > now();
    }

    public function marquerCommeUtilise(): void
    {
        $this->update([
            'utilise'          => true,
            'date_utilisation' => now(),
        ]);
    }

    public function getPrixFormate(): string
    {
        return number_format($this->prix, 2).' €';
    }

    public function getTypeTarifLabel(): string
    {
        return match ($this->type_tarif) {
            'plein'    => 'Plein tarif',
            'reduit'   => 'Tarif réduit',
            'etudiant' => 'Étudiant',
            'senior'   => 'Senior',
            'enfant'   => 'Enfant',
            'groupe'   => 'Groupe',
            default    => 'Standard'
        };
    }

    public function getQRCodeUrl(): ?string
    {
        return $this->qr_code ? asset('storage/qrcodes/'.$this->qr_code) : null;
    }

    public function generateQRCode(): string
    {
        // TODO: Implémenter la génération de QR code
        // Pour l'instant, on génère un code simple
        $code = 'BILLET_'.$this->id.'_'.now()->timestamp;

        $this->update(['qr_code' => $code]);

        return $code;
    }

    protected function casts(): array
    {
        return [
            'prix'             => 'float',
            'utilise'          => 'boolean',
            'date_utilisation' => 'datetime',
        ];
    }
}
