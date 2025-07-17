<?php

declare(strict_types=1);

namespace App\Security\Http\PasswordReset;

use App\Http\Controllers\Controller;
use App\Security\Http\Requests\MotDePasseOublieRequest;
use Illuminate\Http\RedirectResponse;

final class EnvoyerEmailMotDePasseOublie extends Controller
{
    public function __invoke(MotDePasseOublieRequest $request): RedirectResponse
    {
        // Pour l'ECF : on fait semblant d'envoyer l'email
        // En production, on vérifierait si l'email existe mais on retournerait toujours le même message
        // pour ne pas révéler l'existence ou non d'un compte

        return redirect()
            ->route('mot-de-passe-oublie')
            ->with('status', 'Si un compte est associé à cette adresse e-mail, vous recevrez un lien de réinitialisation de mot de passe dans quelques minutes.');
    }
}
