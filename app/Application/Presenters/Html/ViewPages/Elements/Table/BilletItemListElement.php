<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Domain\Entities\Billet\Billet;

/**
 * Element de vue pour un item de billet dans une liste
 */
final readonly class BilletItemListElement
{
    public function __construct(
        public string $id,
        public string $numeroBillet,
        public string $place,
        public string $typeTarif,
        public string $prix,
        public string $utiliseBadge,
        public string $dateUtilisation,
        public string $reservationInfo,
        public string $seanceInfo,
        public ?string $qrCode,
        public string $createdAt,
    ) {}

    /**
     * Créer un BilletItemListElement depuis une entité Billet
     */
    public static function creer(Billet $billet): self
    {
        return new self(
            id: $billet->id->uuid,
            numeroBillet: $billet->numeroBillet,
            place: $billet->place,
            typeTarif: $billet->typeTarif->libelle(),
            prix: number_format($billet->prix->getAmount() / 100, 2).' €',
            utiliseBadge: $billet->utilise
                ? '<span class="badge badge-success">Utilisé</span>'
                : '<span class="badge badge-warning">Disponible</span>',
            dateUtilisation: $billet->dateUtilisation
                ? $billet->dateUtilisation->format('d/m/Y H:i')
                : '-',
            reservationInfo: "Rés. {$billet->reservationId->uuid}",
            seanceInfo: "Séance {$billet->seanceId->uuid}",
            qrCode: $billet->qrCode,
            createdAt: $billet->createdAt->format('d/m/Y H:i'),
        );
    }

    /**
     * Vérifier si le billet a un QR code
     */
    public function hasQrCode(): bool
    {
        return $this->qrCode !== null;
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatusClass(): string
    {
        return str_contains($this->utiliseBadge, 'success') ? 'text-green-600' : 'text-yellow-600';
    }
}
