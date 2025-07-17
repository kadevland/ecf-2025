<?php

declare(strict_types=1);

namespace App\Application\UseCases\Client\AfficherClients;

use App\Application\DTOs\PaginationInfo;

/**
 * Response pour afficher les clients
 */
final readonly class AfficherClientsResponse
{
    public function __construct(
        public array $clients,
        public PaginationInfo $pagination,
    ) {}
}
