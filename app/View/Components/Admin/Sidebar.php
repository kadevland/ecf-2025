<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

final class Sidebar extends Component
{
    /**
     * @var array<array{title: string, links: array<array{href: string, label: string, icon: string}>}>
     */
    public array $sidebarSections = [];

    public string $sideBarTitle;

    public function __construct()
    {
        $this->sideBarTitle = 'Menu';

        $this->sidebarSections = [
            ...$this->getMainSection(),
            ...$this->getContentSection(),
            ...$this->getUsersSection(),
            ...$this->getSystemSection(),
        ];
    }

    public function render(): View
    {
        return view('components.admin.sidebar');
    }

    /**
     * @return array<array{title: string, links: array<array{href: string, label: string, icon: string}>}>
     */
    private function getMainSection(): array
    {
        return [
            [
                'title' => 'Principal',
                'links' => [
                    [
                        'href'  => route('gestion.dashboard'),
                        'label' => 'Tableau de bord',
                        'icon'  => Blade::render('<x-lucide-home class="w-5 h-5" />'),
                    ],
                    [
                        'href'  => '#',
                        'label' => 'Analytics',
                        'icon'  => Blade::render('<x-lucide-trending-up class="w-5 h-5" />'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<array{title: string, links: array<array{href: string, label: string, icon: string}>}>
     */
    private function getContentSection(): array
    {
        return [
            [
                'title' => 'Contenu',
                'links' => [
                    [
                        'href'  => route('gestion.supervision.cinemas.index'),
                        'label' => 'Cinémas',
                        'icon'  => Blade::render('<x-lucide-building class="w-5 h-5" />'),
                    ],
                    [
                        'href'  => route('gestion.supervision.films.index'),
                        'label' => 'Films',
                        'icon'  => Blade::render('<x-lucide-film class="w-5 h-5" />'),
                    ],
                    [
                        'href'  => route('gestion.supervision.seances.index'),
                        'label' => 'Séances',
                        'icon'  => Blade::render('<x-lucide-calendar class="w-5 h-5" />'),
                    ],
                    [
                        'href'  => route('gestion.supervision.reservations.index'),
                        'label' => 'Réservations',
                        'icon'  => Blade::render('<x-lucide-ticket class="w-5 h-5" />'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<array{title: string, links: array<array{href: string, label: string, icon: string}>}>
     */
    private function getUsersSection(): array
    {
        return [
            [
                'title' => 'Utilisateurs',
                'links' => [
                    [
                        'href'  => route('gestion.supervision.clients.index'),
                        'label' => 'Clients',
                        'icon'  => Blade::render('<x-lucide-users class="w-5 h-5" />'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<array{title: string, links: array<array{href: string, label: string, icon: string}>}>
     */
    private function getSystemSection(): array
    {
        return [
            [
                'title' => 'Système',
                'links' => [
                    [
                        'href'  => route('gestion.supervision.incidents.index'),
                        'label' => 'Incidents',
                        'icon'  => Blade::render('<x-lucide-alert-triangle class="w-5 h-5" />'),
                    ],
                    [
                        'href'  => '#',
                        'label' => 'Paramètres',
                        'icon'  => Blade::render('<x-lucide-settings class="w-5 h-5" />'),
                    ],
                ],
            ],
        ];
    }
}
