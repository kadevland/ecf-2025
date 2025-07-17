<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use Illuminate\Support\Collection;

final readonly class ActionListView
{
    /**
     * @param  Collection<int, Action>  $actions
     */
    public function __construct(
        public readonly Collection $actions,
        public readonly bool $isDropdown = false,
        public readonly bool $isInline = true,
        public readonly string $dropdownLabel = 'Actions',
        public readonly string $dropdownIcon = 'M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z',
    ) {}

    /**
     * Vérifie si la liste contient des actions
     */
    public function hasActions(): bool
    {
        return $this->actions->isNotEmpty();
    }

    /**
     * Retourne les actions activées
     */
    public function enabledActions(): Collection
    {
        return $this->actions->filter(fn (Action $action) => $action->enabled);
    }

    /**
     * Vérifie si il y a des actions activées
     */
    public function hasEnabledActions(): bool
    {
        return $this->enabledActions()->isNotEmpty();
    }

    /**
     * Retourne le nombre d'actions
     */
    public function count(): int
    {
        return $this->actions->count();
    }

    /**
     * Retourne le style d'affichage à utiliser
     */
    public function displayStyle(): string
    {
        if ($this->isDropdown) {
            return 'dropdown';
        }

        return $this->isInline ? 'inline' : 'stacked';
    }

    /**
     * Vérifie si on doit afficher en dropdown automatiquement
     */
    public function shouldUseDropdown(): bool
    {
        return $this->isDropdown || $this->enabledActions()->count() > 3;
    }
}
