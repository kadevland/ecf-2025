<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use App\Application\UseCases\Client\AfficherClients\AfficherClientsRequest;
use App\Domain\Enums\UserStatus;
use Illuminate\Http\Request;

/**
 * Mapper pour convertir les requêtes HTTP en objets de domaine pour les clients
 */
final class ClientHttpMapper extends AbstractHttpMapper
{
    public function toAfficherClientsRequest(Request $request): AfficherClientsRequest
    {
        $status = null;
        if ($request->has('status') && $request->get('status') !== '') {
            $status = UserStatus::from($request->get('status'));
        }

        return new AfficherClientsRequest(
            status: $status,
            search: $request->get('search'),
            sortBy: $request->get('sort', 'created_at'),
            sortDirection: $request->get('direction', 'desc'),
            limit: (int) $request->get('limit', 20),
            offset: (int) $request->get('offset', 0),
        );
    }

    public function toDTO(Request $request): object
    {
        return $this->toAfficherClientsRequest($request);
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'status'    => 'nullable|string|in:PendingVerification,Active,Suspended',
            'search'    => 'nullable|string|max:255',
            'sort'      => 'nullable|string|in:created_at,email,nom,prenom,status',
            'direction' => 'nullable|string|in:asc,desc',
            'limit'     => 'nullable|integer|min:1|max:100',
            'offset'    => 'nullable|integer|min:0',
        ]);
    }
}
