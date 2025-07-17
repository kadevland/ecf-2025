<?php

declare(strict_types=1);

namespace App\View\Components\App\Auth;

use Illuminate\Contracts\View\View;

final class Menu extends MenuBase
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.app.auth.menu');
    }
}
