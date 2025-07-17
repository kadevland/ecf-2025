<?php

declare(strict_types=1);

namespace App\View\Components\App\Auth;

use Illuminate\Contracts\View\View;

final class MenuMobile extends MenuBase
{
    public function render(): View
    {
        return view('components.app.auth.menu-mobile');
    }
}
