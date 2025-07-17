<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

abstract readonly class ItemListElement
{
    public function __construct(
        public readonly ActionListView $actions,
    ) {}

    /**
     * Affiche la valeur pour une colonne donnée
     */
    abstract public function displayValue(string $columnKey): mixed;

    /**
     * Vérifie si l'item a des actions
     */
    final public function hasActions(): bool
    {
        return $this->actions->hasEnabledActions();
    }
}
