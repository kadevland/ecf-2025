<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\AfficherClients;

use App\Domain\Contracts\Repositories\User\ClientCriteria;
use App\Http\Requests\Admin\Client\ClientSearchRequest;

/**
 * Mapper pour AfficherClientsUseCase
 */
final class AfficherClientsMapper
{
    public function mapToRequest(ClientSearchRequest $searchRequest): AfficherClientsRequest
    {
        $validated = $searchRequest->validated();

        return new AfficherClientsRequest(
            recherche: $validated['recherche'] ?? null,
            page: $validated['page']           ?? null,
            perPage: $validated['perPage']     ?? null,
        );
    }

    public function mapToCriteria(AfficherClientsRequest $request): ClientCriteria
    {
        return new ClientCriteria(
            recherche: $request->recherche,
            page: $request->page,
            perPage: $request->perPage,
        );
    }
}
