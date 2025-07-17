<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\User\Components\Profiles\EmployeeProfile;
use App\Domain\Entities\User\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class EmployeeViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    // ✅ Propriétés publiques déjà formatées pour la vue
    public User $user;

    public EmployeeProfile $profile;

    public string $id;

    public string $email;

    public string $nom;

    public string $prenom;

    public string $nomComplet;

    public string $numeroEmploye;

    public string $position;

    public string $cinemaId;

    public string $status;

    public string $classeBadgeStatut;

    public string $classeBadgePosition;

    public bool $estActif;

    public bool $estManager;

    public bool $estEmailVerifie;

    public bool $peutSeConnecter;

    public ?string $dateVerificationEmail;

    public string $date;

    public function __construct(User $user)
    {
        if (! $user->estEmploye()) {
            throw new InvalidArgumentException('L\'utilisateur doit être un employé');
        }

        /** @var EmployeeProfile $profile */
        $profile = $user->profile;

        $this->user    = $user;
        $this->profile = $profile;
        $this->id      = $user->id->uuid;
        $this->email   = $user->email->value;
        $this->nom     = $profile->lastName();
        $this->prenom  = $profile->firstName();

        // ✅ Toutes les propriétés calculées dans le constructeur
        $this->nomComplet            = $this->formatNomComplet($profile);
        $this->numeroEmploye         = $profile->employeeNumber();
        $this->position              = $this->formatPosition($profile);
        $this->cinemaId              = $profile->cinemaId()->uuid;
        $this->status                = $this->formatStatus($user);
        $this->classeBadgeStatut     = $this->formatClasseBadgeStatut($user);
        $this->classeBadgePosition   = $this->formatClasseBadgePosition($profile);
        $this->estActif              = $user->estActif();
        $this->estManager            = $profile->isManager();
        $this->estEmailVerifie       = $user->estEmailVerifie();
        $this->peutSeConnecter       = $user->peutSeConnecter();
        $this->dateVerificationEmail = $user->emailVerifiedAt?->format(self::DATE_FORMAT);
        $this->date                  = $user->createdAt->format(self::DATE_FORMAT);
    }

    // ✅ Fonctions privées d'aide au formatage
    private function formatNomComplet(EmployeeProfile $profile): string
    {
        return mb_trim($profile->firstName().' '.$profile->lastName());
    }

    private function formatPosition(EmployeeProfile $profile): string
    {
        return Str::ucfirst($profile->position());
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

    private function formatClasseBadgePosition(EmployeeProfile $profile): string
    {
        return $profile->isManager()
            ? 'bg-purple-100 text-purple-800'
            : 'bg-blue-100 text-blue-800';
    }
}
