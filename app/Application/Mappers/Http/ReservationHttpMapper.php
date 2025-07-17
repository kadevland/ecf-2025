<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use App\Application\UseCases\Reservation\AfficherReservations\AfficherReservationsRequest;
use App\Domain\Enums\StatutReservation;
use Illuminate\Http\Request;

/**
 * Mapper pour convertir les requêtes HTTP en objets de domaine pour les réservations
 */
final class ReservationHttpMapper extends AbstractHttpMapper
{
    public function toAfficherReservationsRequest(Request $request): AfficherReservationsRequest
    {
        $statut = null;
        if ($request->has('statut') && $request->get('statut') !== '') {
            $statut = StatutReservation::from($request->get('statut'));
        }

        return new AfficherReservationsRequest(
            statut: $statut,
            search: $request->get('search'),
            sortBy: $request->get('sort', 'created_at'),
            sortDirection: $request->get('direction', 'desc'),
            limit: (int) $request->get('limit', 20),
            offset: (int) $request->get('offset', 0),
        );
    }

    public function toDTO(Request $request): object
    {
        return $this->toAfficherReservationsRequest($request);
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'statut'    => 'nullable|string|in:en_attente,confirmee,payee,annulee,terminee,expiree',
            'search'    => 'nullable|string|max:255',
            'sort'      => 'nullable|string|in:created_at,statut,prix_total,nombre_places',
            'direction' => 'nullable|string|in:asc,desc',
            'limit'     => 'nullable|integer|min:1|max:100',
            'offset'    => 'nullable|integer|min:0',
        ]);
    }
}
