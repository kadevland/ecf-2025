<?php

declare(strict_types=1);

namespace App\View\Components\Cinema;

use App\Application\Presenters\Html\ViewModels\CinemaViewModel;
use Illuminate\View\Component;

final class CinemaCard extends Component
{
    public function __construct(
        public CinemaViewModel $cinema,
        public bool $showActions = false,
        public string $size = 'default',
        public bool $showHoraires = true,
        public bool $showAccessibilite = true
    ) {}

    public function render()
    {
        return view('components.cinema.cinema-card');
    }

    /**
     * Classe CSS selon la taille
     */
    public function sizeClass(): string
    {
        return match ($this->size) {
            'compact' => 'p-4',
            'large'   => 'p-8',
            default   => 'p-6',
        };
    }

    /**
     * Affichage des informations selon la taille
     */
    public function showFullInfo(): bool
    {
        return $this->size !== 'compact';
    }
}
