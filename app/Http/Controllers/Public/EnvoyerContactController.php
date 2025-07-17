<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;

final class EnvoyerContactController extends Controller
{
    public function __invoke(ContactRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Pour l'ECF : juste un message de confirmation
        // En production : envoyer vraiment l'email

        return redirect()
            ->route('contact.index')
            ->with('success', 'Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    }
}
