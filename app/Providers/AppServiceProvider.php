<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\Services\PdfService;
use App\Infrastructure\Services\QrCodeService;
use App\Models\Administrator;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(QrCodeService::class);
        $this->app->singleton(PdfService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure DaisyUI pagination view
        Paginator::defaultView('pagination.daisyui');
        Paginator::defaultSimpleView('pagination.simple-daisyui');

        // Configure morphMap for User profiles
        Relation::morphMap([
            'client'        => Client::class,
            'employee'      => Employee::class,
            'administrator' => Administrator::class,
        ]);
    }
}
