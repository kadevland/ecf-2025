<?php

declare(strict_types=1);

namespace App\Domain\Collections\User;

use App\Domain\Collections\Collection;
use App\Domain\Entities\User\User;
use App\Domain\Enums\UserStatus;
use InvalidArgumentException;

/**
 * Collection spécialisée pour les clients
 * Force le type Client pour tous les éléments
 *
 * @extends Collection<User>
 */
final class ClientCollection extends Collection
{
    /**
     * Trouve un client par son email
     */
    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->find(fn (User $user) => $user->email->value === $email);
    }

    /**
     * Filtre les clients par statut
     */
    public function filterByStatus(UserStatus $status): self
    {
        return $this->filter(fn (User $user) => $user->statut === $status);
    }

    /**
     * Filtre les clients actifs
     */
    public function filterActifs(): self
    {
        return $this->filterByStatus(UserStatus::Active);
    }

    /**
     * Filtre les clients suspendus
     */
    public function filterSuspendus(): self
    {
        return $this->filterByStatus(UserStatus::Suspended);
    }

    /**
     * Filtre les clients en attente de vérification
     */
    public function filterEnAttenteVerification(): self
    {
        return $this->filterByStatus(UserStatus::PendingVerification);
    }

    /**
     * Filtre les clients avec email vérifié
     */
    public function filterEmailVerifies(): self
    {
        return $this->filter(fn (User $user) => $user->estEmailVerifie());
    }

    /**
     * Filtre les clients avec email non vérifié
     */
    public function filterEmailNonVerifies(): self
    {
        return $this->filter(fn (User $user) => ! $user->estEmailVerifie());
    }

    /**
     * Filtre les clients qui peuvent se connecter
     */
    public function filterPeventSeConnecter(): self
    {
        return $this->filter(fn (User $user) => $user->peutSeConnecter());
    }

    /**
     * Recherche dans les informations du client
     */
    public function searchInProfile(string $search): self
    {
        $search = mb_strtolower($search);

        return $this->filter(function (User $user) use ($search) {
            $profile = $user->profile;

            // Recherche dans prénom et nom
            if (str_contains(mb_strtolower($profile->firstName), $search) ||
                str_contains(mb_strtolower($profile->lastName), $search)) {
                return true;
            }

            // Recherche dans email
            if (str_contains(mb_strtolower($user->email->value), $search)) {
                return true;
            }

            // Recherche dans téléphone (si ClientProfile)
            if ($profile instanceof \App\Domain\Entities\User\Components\Profiles\ClientProfile) {
                $phone = $profile->phone();
                if ($phone && str_contains(mb_strtolower($phone), $search)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Trie les clients par nom de famille
     */
    public function sortByLastName(bool $ascending = true): self
    {
        return $this->sort(function (User $a, User $b) use ($ascending) {
            $comparison = strcasecmp($a->profile->lastName, $b->profile->lastName);

            return $ascending ? $comparison : -$comparison;
        });
    }

    /**
     * Trie les clients par prénom
     */
    public function sortByFirstName(bool $ascending = true): self
    {
        return $this->sort(function (User $a, User $b) use ($ascending) {
            $comparison = strcasecmp($a->profile->firstName, $b->profile->firstName);

            return $ascending ? $comparison : -$comparison;
        });
    }

    /**
     * Trie les clients par email
     */
    public function sortByEmail(bool $ascending = true): self
    {
        return $this->sort(function (User $a, User $b) use ($ascending) {
            $comparison = strcasecmp($a->email->value, $b->email->value);

            return $ascending ? $comparison : -$comparison;
        });
    }

    /**
     * Trie les clients par date de création
     */
    public function sortByCreatedAt(bool $ascending = true): self
    {
        return $this->sort(function (User $a, User $b) use ($ascending) {
            $comparison = $a->createdAt->compare($b->createdAt);

            return $ascending ? $comparison : -$comparison;
        });
    }

    /**
     * Validation stricte : seuls les clients sont autorisés
     */
    protected function validateItem(mixed $item): void
    {
        if (! $item instanceof User) {
            throw new InvalidArgumentException('ClientCollection ne peut contenir que des instances de User');
        }

        if (! $item->estClient()) {
            throw new InvalidArgumentException('ClientCollection ne peut contenir que des utilisateurs de type Client');
        }
    }
}
