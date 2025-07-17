<?php

declare(strict_types=1);

namespace App\Security\Http\Logout;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class Deconnexion extends Controller
{
    /**
     * Déconnecter l'utilisateur
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Déconnexion de l'utilisateur
        Auth::logout();

        // Invalidation de la session
        $request->session()->invalidate();

        // Régénération du token CSRF
        $request->session()->regenerateToken();

        // Redirection vers la page d'accueil
        return redirect('/');
    }
}
