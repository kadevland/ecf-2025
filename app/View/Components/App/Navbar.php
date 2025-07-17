<?php

declare(strict_types=1);

namespace App\View\Components\App;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

final class Navbar extends Component
{
    /**
     * @var array<array{href: string, label: string, icon: string }>
     */
    public array $navLinks;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->navLinks = [
            [
                'href'  => route('accueil'),
                'label' => 'Accueil',
                'icon'  => Blade::render('<x-lucide-house class="w-5 h-5" />'),
            ],
            [
                'href'  => route('films.index'),
                'label' => 'Films',
                'icon'  => Blade::render('<x-lucide-film class="w-5 h-5 "/>'),
            ],
            [
                'href'  => route('cinemas.index'),
                'label' => 'Cinémas',
                'icon'  => Blade::render('<x-lucide-map-pin class="w-5 h-5 "/>'),
            ],
            [
                'href'  => route('contact.index'),
                'label' => 'Contact',
                'icon'  => Blade::render('<x-lucide-contact class="w-5 h-5 "/>'),
            ],
        ];
    }

    public function render(): View
    {
        return view('components.app.navbar');
    }
}
