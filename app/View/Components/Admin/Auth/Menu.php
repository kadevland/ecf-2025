<?php

declare(strict_types=1);

namespace App\View\Components\Admin\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

final class Menu extends Component
{
    /**
     * @var array<array{href: string, icon: string, label: string}>
     */
    public array $userMenuLinks = [];

    public bool $isAuthenticated;

    /** @var \App\Models\User|null */
    public ?object $user;

    public string $userInitial;

    public string $userName;

    public string $urlLogout = '#';

    public function __construct()
    {

        $this->user = Auth::user();

        $this->isAuthenticated = true;
        $this->userName        = $this->user?->name ? $this->user->name : 'Admin';
        $this->userInitial     = $this->user?->email ? mb_substr($this->user->email, 0, 1) : 'AD';

        $this->userMenuLinks = [
            ...$this->getAdminMenuLinks(),
            ...$this->getSuperAdminMenuLinks(),
            ...$this->getLogoutMenuLink(),
        ];
    }

    public function render(): View
    {
        return view('components.admin.auth.menu');
    }

    /**
     * @return array<array{href: string, icon: string, label: string}>
     */
    private function getAdminMenuLinks(): array
    {
        if (! $this->isAuthenticated) {
            return [];
        }

        return [
            [
                'href'  => '#',
                'label' => 'Mon profil',
                'icon'  => Blade::render('<x-lucide-user class="w-4 h-4 mr-2" />'),
            ],
            [
                'href'  => '#',
                'label' => 'Paramètres',
                'icon'  => Blade::render('<x-lucide-settings class="w-4 h-4 mr-2" />'),
            ],
        ];
    }

    /**
     * @return array<array{href: string, icon: string, label: string}>
     */
    private function getSuperAdminMenuLinks(): array
    {
        if (! $this->isAuthenticated) {
            return [];
        }

        return [
            [
                'href'  => '#',
                'label' => 'Système',
                'icon'  => Blade::render('<x-lucide-cpu class="w-4 h-4 mr-2" />'),
            ],
        ];
    }

    /**
     * @return array<array{href: string, icon: string, label: string}>
     */
    private function getLogoutMenuLink(): array
    {
        if (! $this->isAuthenticated) {
            return [];
        }

        return [
            [
                'href'   => $this->urlLogout,
                'label'  => 'Déconnexion',
                'icon'   => Blade::render('<x-lucide-log-out class="w-4 h-4 mr-2" />'),
                'action' => 'logout',
            ],
        ];
    }
}
