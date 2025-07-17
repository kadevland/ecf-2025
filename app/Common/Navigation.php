<?php

declare(strict_types=1);

namespace App\Common;

use App\Domain\ValueObjects\Cinema\CinemaId;

final readonly class Navigation
{
    /**
     * 🌍 ROUTES PUBLIQUES
     */
    public static function public(): PublicNavigation
    {
        return new PublicNavigation();
    }

    /**
     * 👤 ROUTES CLIENT
     */
    public static function client(): ClientNavigation
    {
        return new ClientNavigation();
    }

    /**
     * 🛡️ ROUTES GESTION (Administration)
     */
    public static function gestion(): GestionNavigation
    {
        return new GestionNavigation();
    }
}

/**
 * Navigation publique
 */
final readonly class PublicNavigation
{
    public function accueil(): string
    {
        return route('accueil');
    }

    public function films(): string
    {
        return route('films.index');
    }

    public function cinemas(): string
    {
        return route('cinemas.index');
    }

    public function contact(): string
    {
        return route('contact.index');
    }

    public function contactEnvoyer(): string
    {
        return route('contact.envoyer');
    }

    public function connexion(): string
    {
        return route('connexion');
    }

    public function connexionStore(): string
    {
        return route('connexion.store');
    }

    public function deconnexion(): string
    {
        return route('deconnexion');
    }

    public function creerCompte(): string
    {
        return route('creer-compte');
    }

    public function creerCompteStore(): string
    {
        return route('creer-compte.store');
    }

    public function motDePasseOublie(): string
    {
        return route('mot-de-passe-oublie');
    }

    public function motDePasseOublieEnvoyer(): string
    {
        return route('mot-de-passe-oublie.envoyer');
    }
}

/**
 * Navigation client authentifié
 */
final readonly class ClientNavigation
{
    public function monCompte(): string
    {
        return route('mon-compte');
    }

    public function mesReservations(): string
    {
        return route('mes-reservations');
    }

    // TODO: Futures routes client
    // public function reserverSeance(int $seanceId): string
    // {
    //     return route('reserver.seance', $seanceId);
    // }

    // public function noterFilm(int $filmId): string
    // {
    //     return route('noter.film', $filmId);
    // }
}

/**
 * Navigation gestion/administration
 */
final readonly class GestionNavigation
{
    public function dashboard(): string
    {
        return route('gestion.dashboard');
    }

    public function supervision(): SupervisionNavigation
    {
        return new SupervisionNavigation();
    }
}

/**
 * Navigation supervision (sous-section de gestion)
 */
final readonly class SupervisionNavigation
{
    public function cinemas(): string
    {
        return route('gestion.supervision.cinemas.index');
    }

    public function salles(CinemaId $cinemaId): string
    {
        return route('gestion.supervision.salles.index', $cinemaId->uuid);
    }

    public function seances(): string
    {
        return route('gestion.supervision.seances.index');
    }

    public function films(): string
    {
        return route('gestion.supervision.films.index');
    }

    public function reservations(): string
    {
        return route('gestion.supervision.reservations.index');
    }
}
