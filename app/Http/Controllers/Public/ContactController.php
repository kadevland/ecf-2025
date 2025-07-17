<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

final class ContactController extends Controller
{
    public function __invoke(): \Illuminate\Contracts\View\View
    {
        return view('app.contact.index');
    }
}
