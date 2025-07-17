<?php

declare(strict_types=1);

namespace App\Security\Http\Login;

use App\Http\Controllers\Controller;
use App\Security\Http\Requests\ConnexionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class Connexion extends Controller
{
    /**
     * Traiter la tentative de connexion
     */
    public function __invoke(ConnexionRequest $request): RedirectResponse
    {
        // Récupération des credentials validés
        $credentials = $request->getCredentials();

        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            // Régénération de la session pour sécurité
            $request->session()
                ->regenerate();

            // Redirection selon le type d'utilisateur
            $user = Auth::user();

            return match ($user->type) {
                'admin', 'employee' => redirect()->intended('/dashboard'),
                'client'            => redirect()->intended('/'),
                default             => redirect()->intended('/'),
            };
        }

        // Échec de la connexion
        throw ValidationException::withMessages([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }
}
