<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class SceanceController extends Controller
{
    public function __invoke(): View
    {
        // TODO Phase 2: Récupérer vraies données séances

        $breadcrumbs = [
            ['label' => 'Dashboard', 'href' => route('gestion.dashboard')],
            ['label' => 'Séances', 'href' => null],
        ];

        return view('admin.seances.index', ['breadcrumbs' => $breadcrumbs]);
    }
}
