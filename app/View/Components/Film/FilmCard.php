<?php

declare(strict_types=1);

namespace App\View\Components\Film;

use App\Application\Presenters\Html\ViewModels\FilmViewModel;
use Illuminate\View\Component;

final class FilmCard extends Component
{
    public function __construct(
        public FilmViewModel $film,
        public bool $showActions = false,
        public string $size = 'default',
        public bool $showSynopsis = true,
        public bool $showNote = true,
        public bool $showSeances = false
    ) {}

    public function render()
    {
        return view('components.film.film-card');
    }

    /**
     * Classe CSS selon la taille
     */
    public function sizeClass(): string
    {
        return match ($this->size) {
            'compact' => 'w-48',
            'large'   => 'w-96',
            default   => 'w-72',
        };
    }

    /**
     * Affichage des informations selon la taille
     */
    public function showFullInfo(): bool
    {
        return $this->size !== 'compact';
    }

    /**
     * Longueur du synopsis selon la taille
     */
    public function synopsisLength(): int
    {
        return match ($this->size) {
            'compact' => 80,
            'large'   => 200,
            default   => 120,
        };
    }
}
