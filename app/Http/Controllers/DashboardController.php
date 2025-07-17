<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // TODO Phase 2: Récupérer vraies données selon user type
        return view('dashboard');
    }
}
