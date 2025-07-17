<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\User\User;

/**
 * ViewModel pour les clients
 */
final readonly class ClientViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    public string $id;

    public string $email;

    public string $nom;

    public string $prenom;

    public string $telephone;

    public string $statut;

    public string $date;

    private User $user;

    public function __construct(
        User $user
    ) {

        $this->user      = $user;
        $this->id        = $user->id->uuid;
        $this->email     = $user->email->value;
        $this->nom       = $user->profile->firstName;
        $this->prenom    = $user->profile->lastName;
        $this->telephone = $user->profile->phone;
        $this->statut    = $user->statut->value;
        $this->date      = $user->createdAt->format(self::DATE_FORMAT);

    }

    public function id(): string
    {
        return $this->user->id()
            ->uuid;
    }

    public function email(): string
    {
        return $this->user->email()
            ->value;
    }

    public function userType(): string
    {
        return $this->user->userType()
            ->value;
    }

    public function status(): string
    {
        return $this->user->status()
            ->value;
    }

    public function statusLabel(): string
    {
        return match ($this->user->status()
            ->value) {
            'PendingVerification' => 'En attente',
            'Active'              => 'Actif',
            'Suspended'           => 'Suspendu',
            default               => $this->user->status()
                ->value,
        };
    }

    public function statusBadge(): string
    {
        return match ($this->user->status()
            ->value) {
            'PendingVerification' => 'badge-warning',
            'Active'              => 'badge-success',
            'Suspended'           => 'badge-error',
            default               => 'badge-ghost',
        };
    }

    public function nomComplet(): string
    {
        return $this->user->obtenirNomComplet();
    }

    public function dateCreation(): string
    {
        return $this->user->createdAt()
            ->format('d/m/Y H:i');
    }

    public function dateCreationIso(): string
    {
        return $this->user->createdAt()
            ->toISOString();
    }

    public function dateVerificationEmail(): ?string
    {
        return $this->user->emailVerifiedAt()
            ?->format('d/m/Y H:i');
    }

    public function estActif(): bool
    {
        return $this->user->estActif();
    }

    public function estSuspendu(): bool
    {
        return $this->user->estSuspendu();
    }

    public function estEmailVerifie(): bool
    {
        return $this->user->estEmailVerifie();
    }

    public function estClient(): bool
    {
        return $this->user->estClient();
    }

    // Données mockées pour les statistiques client
    public function nombreReservations(): int
    {
        return rand(0, 50);
    }

    public function nombreReservationsAnnulees(): int
    {
        return rand(0, 5);
    }

    public function montantTotalDepense(): string
    {
        $montant = rand(0, 500000) / 100; // En centimes

        return number_format($montant, 2, ',', ' ').' €';
    }

    public function dernierFilmVu(): string
    {
        $films = ['Dune', 'Avatar', 'Oppenheimer', 'Barbie', 'Fast & Furious', 'Spider-Man'];

        return $films[array_rand($films)];
    }

    public function derniereVisite(): string
    {
        return $this->user->updatedAt()
            ->subDays(rand(0, 30))
            ->format('d/m/Y');
    }

    public function cinemaPreferee(): string
    {
        $cinemas = ['Cinéma Central', 'Cinéma Nord', 'Cinéma Sud', 'Cinéma Ouest'];

        return $cinemas[array_rand($cinemas)];
    }

    public function noteMoviennes(): string
    {
        return number_format(rand(150, 500) / 100, 1).'/5';
    }
}
