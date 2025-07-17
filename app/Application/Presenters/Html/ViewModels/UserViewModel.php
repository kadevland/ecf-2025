<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\User\User;
use Illuminate\Support\Str;

final readonly class UserViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    // ✅ Propriétés publiques déjà formatées pour la vue
    public User $user;

    public string $id;

    public string $email;

    public string $userType;

    public string $status;

    public string $classeBadgeStatut;

    public string $classeBadgeType;

    public bool $estActif;

    public bool $estEmailVerifie;

    public bool $estSuspendu;

    public bool $peutSeConnecter;

    public ?string $dateVerificationEmail;

    public string $date;

    public function __construct(User $user)
    {
        $this->user  = $user;
        $this->id    = $user->id->uuid;
        $this->email = $user->email->value;

        // ✅ Toutes les propriétés calculées dans le constructeur
        $this->userType              = $this->formatUserType($user);
        $this->status                = $this->formatStatus($user);
        $this->classeBadgeStatut     = $this->formatClasseBadgeStatut($user);
        $this->classeBadgeType       = $this->formatClasseBadgeType($user);
        $this->estActif              = $user->estActif();
        $this->estEmailVerifie       = $user->estEmailVerifie();
        $this->estSuspendu           = $user->estSuspendu();
        $this->peutSeConnecter       = $user->peutSeConnecter();
        $this->dateVerificationEmail = $user->emailVerifiedAt?->format(self::DATE_FORMAT);
        $this->date                  = $user->createdAt->format(self::DATE_FORMAT);
    }

    // ✅ Fonctions privées d'aide au formatage
    private function formatUserType(User $user): string
    {
        return match ($user->userType) {
            default => Str::ucfirst($user->userType->label()),
        };
    }

    private function formatStatus(User $user): string
    {
        return match ($user->statut) {
            default => Str::ucfirst($user->statut->label()),
        };
    }

    private function formatClasseBadgeStatut(User $user): string
    {
        return match ($user->statut) {
            default => $user->estActif()
                ? 'bg-green-100 text-green-800'
                : ($user->estSuspendu()
                    ? 'bg-red-100 text-red-800'
                    : 'bg-yellow-100 text-yellow-800'),
        };
    }

    private function formatClasseBadgeType(User $user): string
    {
        return match ($user->userType) {
            default => $user->estAdministrateur()
                ? 'bg-purple-100 text-purple-800'
                : ($user->estEmploye()
                    ? 'bg-blue-100 text-blue-800'
                    : 'bg-gray-100 text-gray-800'),
        };
    }
}
