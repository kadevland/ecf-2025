<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\StatusCinema;
use App\Models\Traits\HasSqids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Eloquent Cinema
 *
 * @property string $id
 * @property string $code_cinema
 * @property string $nom
 * @property string|null $description
 * @property array $adresse
 * @property array|null $coordonnees_gps
 * @property string|null $telephone
 * @property string|null $email
 * @property StatusCinema $statut
 * @property array|null $horaires_ouverture
 * @property array|null $services
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class Cinema extends Model
{
    use HasFactory, HasSqids;

    // Utilise l'ID auto-increment par défaut de Laravel

    protected $fillable = [
        'uuid',
        'code_cinema',
        'nom',
        'description',
        'adresse',
        'coordonnees_gps',
        'telephone',
        'email',
        'statut',
        'horaires_ouverture',
        'services',
    ];

    // Relations
    public function salles(): HasMany
    {
        return $this->hasMany(Salle::class);
    }

    // Méthodes helper
    public function isActif(): bool
    {
        return $this->statut === StatusCinema::Actif;
    }

    public function isInactif(): bool
    {
        return $this->statut === StatusCinema::Inactif;
    }

    public function isMaintenance(): bool
    {
        return $this->statut === StatusCinema::Maintenance;
    }

    public function getAdresseComplete(): string
    {
        $adresse = $this->adresse;

        if (! is_array($adresse)) {
            return '';
        }

        $parts = array_filter([
            $adresse['rue']         ?? '',
            $adresse['code_postal'] ?? '',
            $adresse['ville']       ?? '',
            $adresse['pays']        ?? '',
        ]);

        return implode(', ', $parts);
    }

    public function hasGPS(): bool
    {
        return ! empty($this->coordonnees_gps['latitude']) && ! empty($this->coordonnees_gps['longitude']);
    }

    public function getLatitude(): ?float
    {
        return $this->coordonnees_gps['latitude'] ?? null;
    }

    public function getLongitude(): ?float
    {
        return $this->coordonnees_gps['longitude'] ?? null;
    }

    public function hasService(string $service): bool
    {
        return in_array($service, $this->services ?? []);
    }

    public function getNombreSalles(): int
    {
        return $this->salles()->count();
    }

    public function getNombreSallesActives(): int
    {
        return $this->salles()->where('etat', 'active')->count();
    }

    protected function casts(): array
    {
        return [
            'statut'             => StatusCinema::class,
            'adresse'            => 'array',
            'coordonnees_gps'    => 'array',
            'horaires_ouverture' => 'array',
            'services'           => 'array',
        ];
    }
}
