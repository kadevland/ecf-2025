<?php

declare(strict_types=1);

namespace App\View\Components\App\Auth;

use App\Common\Navigation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

abstract class MenuBase extends Component
{
    /**
     * @var array<array{href: string,label: string, icon: string}>
     */
    public array $userMenuLinks = [];

    public bool $isAuthenticated;

    public ?object $user;

    public string $userInitial;

    public string $userName;

    public string $urlLogin;

    public string $urlRegister;

    public string $loginLabel;

    public string $registerLabel;

    public function __construct()
    {
        $this->loginLabel    = 'Se connecter';
        $this->registerLabel = "S'inscrire gratuitement";
        $this->urlLogin      = Navigation::public()->connexion();
        $this->urlRegister   = Navigation::public()->creerCompte();

        $this->isAuthenticated = Auth::check();
        $this->user            = Auth::user();
        $this->userName        = $this->user?->name ? $this->user->name : '';
        $this->userInitial     = $this->user?->name ? mb_strtoupper(mb_substr($this->user->name, 0, 1)) : ($this->user?->email ? mb_strtoupper(mb_substr($this->user->email, 0, 1)) : '');

        $this->userMenuLinks = array_merge(
            $this->userMenuLinks,
            $this->getMenuLinksForUserType()
        );

        // Ajouter le lien de déconnexion seulement si l'utilisateur est connecté
        if ($this->isAuthenticated) {
            $this->userMenuLinks = array_merge(
                $this->userMenuLinks,
                $this->getLogoutMenuLink()
            );
        }
    }

    /**
     * Get menu links based on user type
     *
     * @return array<array{href: string,label: string, icon: string}>
     */
    private function getMenuLinksForUserType(): array
    {
        if (! $this->isAuthenticated) {
            return [];
        }

        return match ($this->user?->user_type?->value) {
            'client'        => $this->getClientMenuLinks(),
            'administrator' => $this->getAdministratorMenuLinks(),
            'employee'      => $this->getEmployeeMenuLinks(),
            default         => []
        };
    }

    /**
     * Summary of getClientMenuLinks
     *
     * @return array<array{href: string,label: string, icon: string}>
     */
    private function getClientMenuLinks(): array
    {
        return [
            [
                'href'  => Navigation::client()->monCompte(),
                'label' => 'Mon Compte',
                'icon'  => Blade::render('<x-lucide-user class="w-4 h-4 mr-2" />'),
            ],
            [
                'href'  => Navigation::client()->mesReservations(),
                'label' => 'Mes Réservations',
                'icon'  => Blade::render('<x-lucide-calendar class="w-4 h-4 mr-2" />'),
            ],
        ];
    }

    /**
     * Summary of getAdministratorMenuLinks
     *
     * @return array<array{href: string,label: string, icon: string}>
     */
    private function getAdministratorMenuLinks(): array
    {
        return [
            [
                'href'  => Navigation::gestion()->dashboard(),
                'label' => 'Administration',
                'icon'  => Blade::render('<x-lucide-shield class="w-4 h-4 mr-2" />'),
            ],
        ];
    }

    /**
     * Summary of getEmployeeMenuLinks
     *
     * @return array<array{href: string,label: string, icon: string}>
     */
    private function getEmployeeMenuLinks(): array
    {
        return [
            [
                'href'  => Navigation::gestion()->dashboard(),
                'label' => 'Gestion',
                'icon'  => Blade::render('<x-lucide-briefcase class="w-4 h-4 mr-2" />'),
            ],
        ];
    }

    /**
     * Summary of getLogoutMenuLink
     *
     * @return array<array{action: string, href: string,label: string, icon: string}>
     */
    private function getLogoutMenuLink(): array
    {
        return [
            [
                'href'   => route('deconnexion'),
                'label'  => 'Déconnexion',
                'icon'   => Blade::render('<x-lucide-log-out class="w-4 h-4 mr-2" />'),
                'action' => 'logout',
            ],
        ];
    }
}
