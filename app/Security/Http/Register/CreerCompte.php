<?php

declare(strict_types=1);

namespace App\Security\Http\Register;

use App\Http\Controllers\Controller;
use App\Security\Http\Requests\CreerCompteRequest;
use Illuminate\Http\RedirectResponse;

final class CreerCompte extends Controller
{
    public function __invoke(CreerCompteRequest $request): RedirectResponse
    {
        // Pour l'ECF : on fait semblant de créer le compte
        // En production, on créerait vraiment l'utilisateur ici

        return redirect()
            ->route('connexion')
            ->with('success', 'Compte créé avec succès ! Cependant, il s\'agit d\'un site de démonstration, aucun compte n\'a été réellement créé. Utilisez les identifiants de test fournis pour vous connecter.');
    }
}
