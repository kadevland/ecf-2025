<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BilletController;
use App\Http\Controllers\Admin\CinemaController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SalleController;
use App\Http\Controllers\Admin\SeanceController;
use App\Http\Controllers\DashboardController;
use App\Security\Http\Login\Connexion;
use App\Security\Http\Login\PageConnexion;
use App\Security\Http\Logout\Deconnexion;
use App\Security\Http\PasswordReset\EnvoyerEmailMotDePasseOublie;
use App\Security\Http\PasswordReset\PageMotDePasseOublie;
use App\Security\Http\Register\CreerCompte;
use App\Security\Http\Register\PageCreerCompte;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 🌍 ROUTES PUBLIQUES (Sans authentification)
|--------------------------------------------------------------------------
| Routes accessibles à tous les visiteurs
*/

// Page d'accueil
Route::get('/', App\Http\Controllers\Public\AccueilController::class)->name('accueil');

// Pages d'information publiques
Route::get('/films', App\Http\Controllers\Public\FilmController::class)->name('films.index');
Route::get('/films/{film:sqid}', App\Http\Controllers\Public\FilmDetailController::class)->name('films.show');
Route::get('/cinemas', App\Http\Controllers\Public\CinemaController::class)->name('cinemas.index');
Route::get('/cinemas/{cinema:sqid}', App\Http\Controllers\Public\CinemaDetailController::class)->name('cinemas.show');
Route::get('/contact', App\Http\Controllers\Public\ContactController::class)->name('contact.index');
Route::post('/contact', App\Http\Controllers\Public\EnvoyerContactController::class)->name('contact.envoyer');

// Workflow de réservation
Route::get('/reserver/confirmation', [App\Http\Controllers\Public\ReservationController::class, 'showConfirmation'])->name('reservation.show-confirmation');
Route::post('/reserver/confirmation', [App\Http\Controllers\Public\ReservationController::class, 'confirmReservation'])->name('reservation.confirm');
Route::get('/reserver/seance/{seance:sqid}', [App\Http\Controllers\Public\ReservationController::class, 'selectSeats'])
    ->name('reservation.select-seats');


Route::middleware(['auth', 'role:client'])->post('/reserver/finaliser', [App\Http\Controllers\Public\ReservationController::class, 'finalize'])->name('reservation.finalize');


// Authentification
Route::get('/connexion', PageConnexion::class)->name('connexion');
Route::post('/connexion', Connexion::class)
    ->middleware('throttle:5,1')
    ->name('connexion.store');
Route::post('/deconnexion', Deconnexion::class)->name('deconnexion');

// Inscription
Route::get('/creer-compte', PageCreerCompte::class)->name('creer-compte');
Route::post('/creer-compte', CreerCompte::class)->name('creer-compte.store');

// Mot de passe oublié
Route::get('/mot-de-passe-oublie', PageMotDePasseOublie::class)->name('mot-de-passe-oublie');
Route::post('/mot-de-passe-oublie', EnvoyerEmailMotDePasseOublie::class)->name('mot-de-passe-oublie.envoyer');

/*
|--------------------------------------------------------------------------
| 👤 ROUTES UTILISATEUR (Client) - AUTHENTIFICATION REQUISE
|--------------------------------------------------------------------------
| Routes client protégées par authentification
*/

// Routes publiques pour les réservations (vérification, etc.)
Route::get('/reservations/{reservation:uuid}/verify', App\Http\Controllers\Public\ReservationVerifyController::class)->name('reservations.verify');

// Routes protégées par authentification (clients uniquement)
Route::middleware(['auth', 'role:client'])->group(function () {
    // Gestion du compte client
    Route::get('/mon-compte', App\Http\Controllers\Client\MonCompteController::class)->name('mon-compte');
    Route::get('/mes-reservations', App\Http\Controllers\Client\MesReservationsController::class)->name('mes-reservations');

    // QR Code et PDF nécessitent l'authentification client
    Route::get('/reservations/{reservation:uuid}/qr-code', App\Http\Controllers\Client\ReservationQrCodeController::class)->name('reservations.qr-code');
    Route::get('/reservations/{reservation:uuid}/pdf', App\Http\Controllers\Client\ReservationPdfController::class)->name('reservations.pdf');

    // Futures routes client
    // Route::get('/reserver/{seance}', ReserverSeanceController::class)->name('reserver.seance');
    // Route::post('/noter/{film}', NoterFilmController::class)->name('noter.film');
});

/*
|--------------------------------------------------------------------------
| 🛡️ ROUTES GESTION (Administration)
|--------------------------------------------------------------------------
| Routes réservées aux administrateurs et employés
*/

Route::prefix('gestion')->name('gestion.')
    ->middleware(['auth', 'role:administrator,employee'])
    ->group(function () {

        // Dashboard principal (accessible aux admins et employés)
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Routes de supervision (Administrateurs et employés)
        Route::prefix('supervision')->name('supervision.')
            ->group(function () {
            Route::get('/cinemas', CinemaController::class)->name('cinemas.index');
            Route::get('/cinemas/{cinema:uuid}/salles', SalleController::class)->name('salles.index');
            Route::get('/seances', SeanceController::class)->name('seances.index');
            Route::get('/films', FilmController::class)->name('films.index');
            Route::get('/reservations', ReservationController::class)->name('reservations.index');
            Route::get('/billets', BilletController::class)->name('billets.index');
            Route::get('/incidents', IncidentController::class)->name('incidents.index');
            Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        });

        // Routes spécifiques aux employés
        Route::prefix('employee')->name('employee.')
            ->middleware('role:employee')
            ->group(function () {
            // Route::get('/incidents', EmployeeIncidentController::class)->name('incidents.index');
        });

        // Routes spécifiques aux administrateurs
        Route::prefix('admin')->name('admin.')
            ->middleware('role:administrator')
            ->group(function () {
            // Routes futures pour les admins uniquement
        });
    });
