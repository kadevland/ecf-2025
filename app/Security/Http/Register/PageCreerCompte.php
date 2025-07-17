<?php

declare(strict_types=1);

namespace App\Security\Http\Register;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class PageCreerCompte extends Controller
{
    public function __invoke(): View
    {
        return view('security.creer-compte');
    }
}
