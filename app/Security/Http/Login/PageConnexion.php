<?php

declare(strict_types=1);

namespace App\Security\Http\Login;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class PageConnexion extends Controller
{
    /**
     * Afficher la page de connexion
     */
    public function __invoke(): View
    {
        return view('security.connexion');
    }
}
