<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Footer extends Component
{
    public string $adminVersion;

    public string $lastUpdate;

    public string $supportEmail;

    /**
     * @var array<array{href: string, label: string}>
     */
    public array $adminLinks;

    public function __construct()
    {
        $this->adminVersion = 'v0.0.0';
        $this->lastUpdate   = 'Dernière mise à jour : d:m/Y';
        $this->supportEmail = 'support@cinephoria.fr';

        $this->adminLinks = [
            ...$this->getAdminFooterLinks(),
        ];

    }

    public function render(): View
    {
        return view('components.admin.footer');
    }

    /**
     * @return array<array{href: string, label: string}>
     */
    private function getAdminFooterLinks(): array
    {
        return [
            ['href' => '#', 'label' => 'Documentation'],
            ['href' => '#', 'label' => 'Support'],
            ['href' => '#', 'label' => 'Notes de version'],
        ];
    }
}
