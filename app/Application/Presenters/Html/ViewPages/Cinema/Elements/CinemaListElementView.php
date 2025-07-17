<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Cinema\Elements;

use App\Application\Presenters\Html\ViewModels\CinemaViewModel;
use App\Application\Presenters\Html\ViewPages\Elements\ListElementView;
use Illuminate\Support\Collection;

final readonly class CinemaListElementView extends ListElementView
{
    /**
     * @param  Collection<int, \App\Domain\Entities\Cinema\Cinema>  $cinemas
     * @param  array<string, array<string, mixed>>  $columns
     * @param  array<int, array<string, mixed>>  $actions
     */
    public function __construct(
        public readonly Collection $cinemas,
        public readonly array $columns,
        public readonly array $actions,
        public readonly string $currentSort,
        public readonly string $currentDirection,
        public readonly mixed $pagination,
    ) {}

    /**
     * @return Collection<int, CinemaViewModel>
     */
    public function items(): Collection
    {
        return $this->cinemas->map(fn ($cinema) => new CinemaViewModel($cinema));
    }
}
