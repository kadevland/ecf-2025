<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Collections\User\ClientCollection;
use App\Domain\Entities\User\User;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\User\UserId;

/**
 * Interface pour le repository des clients
 * Force le type Client dans toutes les opérations
 */
interface ClientRepositoryInterface
{
    /**
     * Récupère les clients selon des critères
     */
    public function findByCriteria(ClientCriteria $criteria): ClientCollection;

    /**
     * Récupère les clients paginés selon des critères
     */
    public function findPaginatedByCriteria(ClientCriteria $criteria): \App\Domain\Collections\PaginatedCollection;

    /**
     * Trouve un client par son ID
     */
    public function findById(UserId $id): ?User;

    /**
     * Trouve un client par son email
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Sauvegarde un client (création ou mise à jour)
     */
    public function save(User $client): User;
}
