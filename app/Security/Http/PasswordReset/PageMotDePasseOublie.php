<?php

declare(strict_types=1);

namespace App\Security\Http\PasswordReset;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class PageMotDePasseOublie extends Controller
{
    public function __invoke(): View
    {
        return view('security.mot-de-passe-oublie');
    }
}
