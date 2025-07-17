<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\User\Components\Profiles\AdminProfile;
use App\Domain\Entities\User\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class AdministratorViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    // ✅ Propriétés publiques déjà formatées pour la vue
    public User $user;

    public AdminProfile $profile;

    public string $id;

    public string $email;

    public string $nom;

    public string $prenom;

    public string $nomComplet;

    public string $status;

    public string $niveau;

    public string $classeBadgeStatut;

    public string $classeBadgeNiveau;

    public bool $estActif;

    public bool $estSuperAdmin;

    public bool $estEmailVerifie;

    public bool $peutSeConnecter;

    public ?string $dateVerificationEmail;

    public string $date;

    public function __construct(User $user)
    {
        if (! $user->estAdministrateur()) {
            throw new InvalidArgumentException('L\'utilisateur doit être un administrateur');
        }

        /** @var AdminProfile $profile */
        $profile = $user->profile;

        $this->user    = $user;
        $this->profile = $profile;
        $this->id      = $user->id->uuid;
        $this->email   = $user->email->value;
        $this->nom     = $profile->lastName();
        $this->prenom  = $profile->firstName();

        // ✅ Toutes les propriétés calculées dans le constructeur
        $this->nomComplet            = $this->formatNomComplet($profile);
        $this->status                = $this->formatStatus($user);
        $this->niveau                = $this->formatNiveau($profile);
        $this->classeBadgeStatut     = $this->formatClasseBadgeStatut($user);
        $this->classeBadgeNiveau     = $this->formatClasseBadgeNiveau($profile);
        $this->estActif              = $user->estActif();
        $this->estSuperAdmin         = $profile->isSuperAdmin();
        $this->estEmailVerifie       = $user->estEmailVerifie();
        $this->peutSeConnecter       = $user->peutSeConnecter();
        $this->dateVerificationEmail = $user->emailVerifiedAt?->format(self::DATE_FORMAT);
        $this->date                  = $user->createdAt->format(self::DATE_FORMAT);
    }

    // ✅ Fonctions privées d'aide au formatage
    private function formatNomComplet(AdminProfile $profile): string
    {
        return mb_trim($profile->firstName().' '.$profile->lastName());
    }

    private function formatStatus(User $user): string
    {
        return match ($user->statut) {
            default => Str::ucfirst($user->statut->label()),
        };
    }

    private function formatNiveau(AdminProfile $profile): string
    {
        return $profile->isSuperAdmin() ? 'Super Administrateur' : 'Administrateur';
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

    private function formatClasseBadgeNiveau(AdminProfile $profile): string
    {
        return $profile->isSuperAdmin()
            ? 'bg-red-100 text-red-800'
            : 'bg-purple-100 text-purple-800';
    }
}
