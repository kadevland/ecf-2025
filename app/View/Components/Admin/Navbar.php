<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

final class Navbar extends Component
{
    /**
     * @var array<array{href: string, icon: string, label: string}>
     */
    public array $navLinks = [];

    public function __construct()
    {
        $this->navLinks = [
            ...$this->getMainNavLinks(),
            ...$this->getQuickAccessLinks(),
        ];
    }

    public function render(): View
    {
        return view('components.admin.navbar');
    }

    /**
     * Summary of getMainNavLinks
     *
     * @return array<array{href: string, icon: string, label: string}>
     */
    private function getMainNavLinks(): array
    {
        return [
            [
                'href'  => route('gestion.dashboard'),
                'label' => 'Tableau de bord',
                'icon'  => Blade::render('<x-lucide-home class="w-5 h-5 mr-2" />'),
            ],
            [
                'href'  => route('gestion.supervision.films.index'),
                'label' => 'Films',
                'icon'  => Blade::render('<x-lucide-film class="w-5 h-5 mr-2" />'),
            ],
            [
                'href'  => route('gestion.supervision.cinemas.index'),
                'label' => 'Cinémas',
                'icon'  => Blade::render('<x-lucide-building class="w-5 h-5 mr-2" />'),
            ],
        ];
    }

    /**
     * Summary of getQuickAccessLinks
     *
     * @return array<array{href: string, icon: string, label: string}>
     */
    private function getQuickAccessLinks(): array
    {
        return [
            [
                'href'  => '#',
                'label' => 'Rapports',
                'icon'  => Blade::render('<x-lucide-bar-chart class="w-5 h-5 mr-2" />'),
            ],
        ];
    }
}
