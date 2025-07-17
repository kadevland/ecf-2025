<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Cinema\Elements;

use App\Support\UrlBuilder;

final readonly class CinemaSearchElementView
{
    /** @var array<int, int> */
    public const PER_PAGE_OPTIONS = [1, 15, 25, 50, 100];

    public const PER_PAGE_DEFAULT = 15;

    public function __construct(
        public readonly string $recherche,
        public readonly ?bool $operationnel,
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
     * Vérifie si le formulaire contient des données de recherche
     */
    public function isNotEmpty(): bool
    {
        return $this->recherche !== ''
            || $this->operationnel !== null
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
