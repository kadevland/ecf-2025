<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (! auth()->check()) {
            return redirect()->route('connexion');
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (! in_array($user->user_type->value, $roles)) {
            // Rediriger selon le type d'utilisateur
            return match ($user->user_type->value) {
                'client'        => redirect()->route('mon-compte')->with('error', 'Accès non autorisé'),
                'employee'      => redirect()->route('gestion.dashboard')->with('error', 'Accès non autorisé'),
                'administrator' => redirect()->route('gestion.dashboard')->with('error', 'Accès non autorisé'),
                default         => redirect()->route('accueil')->with('error', 'Accès non autorisé'),
            };
        }

        return $next($request);
    }
}
