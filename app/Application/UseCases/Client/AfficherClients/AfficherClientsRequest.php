<?php

declare(strict_types=1);

namespace App\Application\UseCases\Client\AfficherClients;

use App\Domain\Enums\UserStatus;

/**
 * Request pour afficher les clients
 */
final readonly class AfficherClientsRequest
{
    public function __construct(
        public ?UserStatus $status = null,
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}
