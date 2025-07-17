<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements;

use Illuminate\Support\Collection;

abstract readonly class ListElementView
{
    /**
     * Collection d'éléments à afficher dans la liste
     *
     * @return Collection<int, mixed>
     */
    abstract public function items(): Collection;

    /**
     * Convertit le ListView en array pour la vue
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
