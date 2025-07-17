<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Common\Navigation;
use App\Models\User;

final readonly class MonCompteViewModel
{
    public function __construct(
        private ?User $user = null,
        private int $nombreReservationsCount = 0,
        private int $nombreFilmsVusCount = 0
    ) {}

    /**
     * Informations de base de l'utilisateur
     */
    public function email(): string
    {
        return $this->user?->email ?? 'Non renseigné';
    }

    public function nom(): string
    {
        return $this->user?->name ?? 'Non renseigné';
    }

    public function dateInscription(): string
    {
        return $this->user?->created_at?->format('d/m/Y') ?? 'Non disponible';
    }

    public function initialeUtilisateur(): string
    {
        if (! $this->user?->email) {
            return 'U';
        }

        return mb_strtoupper(mb_substr($this->user->email, 0, 1));
    }

    /**
     * Statistiques utilisateur
     */
    public function nombreReservations(): int
    {
        return $this->nombreReservationsCount;
    }

    public function nombreFilmsVus(): int
    {
        return $this->nombreFilmsVusCount;
    }

    /**
     * Liens et actions
     */
    public function lienMesReservations(): string
    {
        return Navigation::client()->mesReservations();
    }

    public function lienFilms(): string
    {
        return Navigation::public()->films();
    }

    public function lienCinemas(): string
    {
        return Navigation::public()->cinemas();
    }

    /**
     * États et permissions
     */
    public function peutModifierProfil(): bool
    {
        // TODO: Vérifier les permissions
        return false; // Désactivé pour le moment
    }

    public function estUtilisateurConnecte(): bool
    {
        return $this->user !== null;
    }

    /**
     * Messages et états
     */
    public function messageStatistiques(): string
    {
        if (! $this->estUtilisateurConnecte()) {
            return 'Connectez-vous pour voir vos statistiques de réservation.';
        }

        $reservations = $this->nombreReservations();
        $films        = $this->nombreFilmsVus();

        if ($reservations === 0) {
            return "Vous n'avez pas encore effectué de réservation.";
        }

        return "Vous avez effectué {$reservations} réservation(s) et vu {$films} film(s) cette année.";
    }

    public function classeBadgeStatut(): string
    {
        if (! $this->estUtilisateurConnecte()) {
            return 'badge-ghost';
        }

        $reservations = $this->nombreReservations();

        return match (true) {
            $reservations === 0 => 'badge-neutral',
            $reservations < 5   => 'badge-primary',
            $reservations < 10  => 'badge-secondary',
            default             => 'badge-accent'
        };
    }

    public function libelleBadgeStatut(): string
    {
        if (! $this->estUtilisateurConnecte()) {
            return 'Visiteur';
        }

        $reservations = $this->nombreReservations();

        return match (true) {
            $reservations === 0 => 'Nouveau',
            $reservations < 5   => 'Régulier',
            $reservations < 10  => 'Fidèle',
            default             => 'VIP'
        };
    }

    /**
     * Breadcrumbs pour la navigation
     */
    public function breadcrumbs(): array
    {
        return [
            ['label' => 'Accueil', 'url' => Navigation::public()->accueil()],
            ['label' => 'Mon Compte', 'url' => null],
        ];
    }
}
