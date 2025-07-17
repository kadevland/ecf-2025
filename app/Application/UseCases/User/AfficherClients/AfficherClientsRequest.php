<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\AfficherClients;

/**
 * DTO pour la requête d'affichage des clients
 */
final readonly class AfficherClientsRequest
{
    public function __construct(
        public ?string $recherche = null,
        public ?int $page = null,
        public ?int $perPage = null,
    ) {}
}
