<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\AfficherClients;

use App\Domain\Collections\PaginatedCollection;

/**
 * DTO pour la réponse d'affichage des clients
 */
final readonly class AfficherClientsResponse
{
    public function __construct(
        public PaginatedCollection $clients,
        public bool $success = true,
    ) {}
}
