<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

final readonly class Action
{
    public function __construct(
        public readonly string $label,
        public readonly string $url,
        public readonly string $icon = '',
        public readonly string $class = 'btn-primary',
        public readonly bool $enabled = true,
        public readonly ?string $tooltip = null,
        public readonly ?string $confirmMessage = null,
    ) {}

    /**
     * Vérifie si l'action est cliquable
     */
    public function isClickable(): bool
    {
        return $this->enabled && $this->url !== '#';
    }

    /**
     * Retourne les classes CSS pour le bouton
     */
    public function buttonClass(): string
    {
        $baseClass = 'btn btn-sm';

        if (! $this->enabled) {
            return $baseClass.' btn-disabled';
        }

        return $baseClass.' '.$this->class;
    }

    /**
     * Retourne les attributs HTML supplémentaires
     */
    public function attributes(): array
    {
        $attrs = [];

        if ($this->tooltip) {
            $attrs['title']        = $this->tooltip;
            $attrs['data-tooltip'] = $this->tooltip;
        }

        if ($this->confirmMessage) {
            $attrs['onclick'] = "return confirm('{$this->confirmMessage}')";
        }

        if (! $this->enabled) {
            $attrs['disabled'] = 'disabled';
        }

        return $attrs;
    }

    /**
     * Vérifie si l'action a une icône
     */
    public function hasIcon(): bool
    {
        return $this->icon !== '';
    }
}
