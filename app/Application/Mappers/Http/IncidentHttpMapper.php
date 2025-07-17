<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use App\Application\UseCases\Incident\AfficherIncidents\AfficherIncidentsRequest;
use App\Domain\Enums\PrioriteIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\TypeIncident;
use Illuminate\Http\Request;

/**
 * Mapper pour convertir les requêtes HTTP en objets de domaine pour les incidents
 */
final class IncidentHttpMapper extends AbstractHttpMapper
{
    public function toAfficherIncidentsRequest(Request $request): AfficherIncidentsRequest
    {
        $type = null;
        if ($request->has('type') && $request->get('type') !== '') {
            $type = TypeIncident::from($request->get('type'));
        }

        $priorite = null;
        if ($request->has('priorite') && $request->get('priorite') !== '') {
            $priorite = PrioriteIncident::from($request->get('priorite'));
        }

        $statut = null;
        if ($request->has('statut') && $request->get('statut') !== '') {
            $statut = StatutIncident::from($request->get('statut'));
        }

        $seulementActifs = null;
        if ($request->has('actifs_seulement')) {
            $seulementActifs = $request->boolean('actifs_seulement');
        }

        return new AfficherIncidentsRequest(
            type: $type,
            priorite: $priorite,
            statut: $statut,
            seulementActifs: $seulementActifs,
            search: $request->get('search'),
            sortBy: $request->get('sort', 'created_at'),
            sortDirection: $request->get('direction', 'desc'),
            limit: (int) $request->get('limit', 20),
            offset: (int) $request->get('offset', 0),
        );
    }

    public function toDTO(Request $request): object
    {
        return $this->toAfficherIncidentsRequest($request);
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'type'             => 'nullable|string|in:projection,audio,eclairage,climatisation,securite,nettoyage,equipement,siege,autre',
            'priorite'         => 'nullable|string|in:faible,normale,elevee,critique',
            'statut'           => 'nullable|string|in:ouvert,en_cours,resolu,ferme,reporte',
            'actifs_seulement' => 'nullable|boolean',
            'search'           => 'nullable|string|max:255',
            'sort'             => 'nullable|string|in:created_at,priorite,type,statut,titre',
            'direction'        => 'nullable|string|in:asc,desc',
            'limit'            => 'nullable|integer|min:1|max:100',
            'offset'           => 'nullable|integer|min:0',
        ]);
    }
}
