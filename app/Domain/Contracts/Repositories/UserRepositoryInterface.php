<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Domain\Collections\UserCollection;
use App\Domain\Contracts\Repositories\User\UserCriteria;
use App\Domain\Entities\User\User;
use App\Domain\Enums\UserType;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\User\UserId;

/**
 * Interface pour le repository des utilisateurs
 */
interface UserRepositoryInterface
{
    /**
     * Récupère les utilisateurs selon des critères
     */
    public function findByCriteria(UserCriteria $criteria): UserCollection;

    /**
     * Compte les utilisateurs selon des critères
     */
    public function countByCriteria(UserCriteria $criteria): int;

    /**
     * Trouve un utilisateur par son ID
     */
    public function findById(UserId $id): ?User;

    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Trouve tous les utilisateurs d'un type donné
     */
    public function findByType(UserType $type): UserCollection;

    /**
     * Trouve tous les utilisateurs
     */
    public function findAll(): UserCollection;

    /**
     * Sauvegarde un utilisateur (création ou mise à jour)
     */
    public function save(User $user): User;

    /**
     * Supprime un utilisateur par son ID
     */
    public function delete(UserId $id): bool;

    /**
     * Vérifie si un utilisateur existe
     */
    public function exists(UserId $id): bool;

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(Email $email): bool;

    /**
     * Charge le profil d'un utilisateur
     */
    public function loadProfile(User $user): User;
}
