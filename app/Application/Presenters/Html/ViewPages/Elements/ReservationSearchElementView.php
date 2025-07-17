<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements;

use App\Domain\Enums\StatutReservation;
use App\Support\UrlBuilder;

final readonly class ReservationSearchElementView
{
    /** @var array<int, int> */
    public const PER_PAGE_OPTIONS = [15, 25, 50, 100];

    public const PER_PAGE_DEFAULT = 15;

    public function __construct(
        public readonly string $recherche,
        public readonly ?string $statut,
        public readonly int $perPage,
    ) {}

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        return self::PER_PAGE_OPTIONS;
    }

    /**
     * @return array<string, string>
     */
    public function statutOptions(): array
    {
        $options = ['' => 'Tous les statuts'];

        foreach (StatutReservation::cases() as $statut) {
            $options[$statut->value] = $statut->label();
        }

        return $options;
    }

    public function isNotEmpty(): bool
    {
        return $this->recherche !== ''
            || $this->statut !== null
            || $this->perPage !== self::PER_PAGE_DEFAULT;
    }

    /**
     * URL pour réinitialiser le formulaire
     */
    public function resetUrl(): string
    {
        return UrlBuilder::current()
            ->only([])
            ->toString();
    }
}
