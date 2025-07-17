<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\View\Component;

final class DataTable extends Component
{
    public function __construct(
        public array $columns = [],
        public array $rows = [],
        public bool $sortable = true,
        public bool $searchable = true,
        public bool $paginated = true,
        public string $currentSort = '',
        public string $currentDirection = 'asc',
        public string $searchQuery = '',
        public ?object $pagination = null,
        public array $actions = []
    ) {}

    public function render()
    {
        return view('components.admin.data-table');
    }

    /**
     * URL pour le tri
     */
    public function sortUrl(string $column): string
    {
        $direction = ($this->currentSort === $column && $this->currentDirection === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery([
            'sort'      => $column,
            'direction' => $direction,
            'search'    => $this->searchQuery,
        ]);
    }

    /**
     * Classe CSS pour l'indicateur de tri
     */
    public function sortClass(string $column): string
    {
        if ($this->currentSort !== $column) {
            return '';
        }

        return $this->currentDirection === 'asc' ? 'sort-asc' : 'sort-desc';
    }

    /**
     * Icône de tri
     */
    public function sortIcon(string $column): string
    {
        if ($this->currentSort !== $column) {
            return 'M7 10l5 5 5-5z';
        }

        return $this->currentDirection === 'asc'
            ? 'M7 14l5-5 5 5z'  // Flèche vers le haut
            : 'M7 10l5 5 5-5z';  // Flèche vers le bas
    }
}
