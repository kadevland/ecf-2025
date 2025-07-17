<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Mappers
        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Cinema\CinemaEntityMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\CinemaBusinessToConditionsMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Reservation\ReservationEntityMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Reservation\ReservationCriteriaToConditionsMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Incident\IncidentEntityMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Incident\IncidentCriteriaToConditionsMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Billet\BilletEntityMapper::class
        );

        $this->app->singleton(
            \App\Infrastructure\Persistence\Mappers\Billet\BilletCriteriaToConditionsMapper::class
        );

        // Bind Repository Interfaces to their Implementations
        $this->app->bind(
            \App\Domain\Contracts\Repositories\Cinema\CinemaRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentCinemaRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Film\FilmRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentFilmRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Salle\SalleRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentSalleRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Seance\SeanceRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentSeanceRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\User\UserRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentUserRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\User\ClientRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentClientRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Reservation\ReservationRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentReservationRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Billet\BilletRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentBilletRepository::class
        );

        $this->app->bind(
            \App\Domain\Contracts\Repositories\Incident\IncidentRepositoryInterface::class,
            \App\Infrastructure\Persistence\Repositories\EloquentIncidentRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
