<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use Illuminate\Support\Collection;

abstract readonly class ListElement
{
    /**
     * @param  Collection<int, HeaderCell>  $headers
     * @param  Collection<int, ItemListElement>  $items
     */
    public function __construct(
        public readonly Collection $headers,
        public readonly Collection $items,
        public readonly ?ActionListView $actions = null,
        public readonly string $title = '',
        public readonly mixed $pagination = null,
    ) {}

    /**
     * Vérifie si le tableau a des en-têtes
     */
    final public function hasHeaders(): bool
    {
        return $this->headers->isNotEmpty();
    }

    /**
     * Vérifie si le tableau a des items
     */
    final public function hasItems(): bool
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Vérifie s'il y a des actions globales
     */
    final public function hasActions(): bool
    {
        return $this->actions !== null && $this->actions->hasEnabledActions();
    }

    /**
     * Vérifie s'il y a une pagination
     */
    final public function hasPagination(): bool
    {
        return $this->pagination !== null;
    }

    /**
     * Retourne le nombre d'items
     */
    final public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Vérifie si le tableau est vide
     */
    final public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
}
