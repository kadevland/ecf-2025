<?php

declare(strict_types=1);

namespace App\Application\UseCases\Seance\AfficherSeances;

use App\Domain\Enums\EtatSeance;
use App\Domain\Enums\QualiteProjection;
use App\Http\Requests\Admin\Seance\SeanceSearchRequest;
use Carbon\CarbonImmutable;

final readonly class AfficherSeancesMapper
{
    /**
     * Convertit une HTTP Request en DTO pour le UseCase
     */
    public function mapToRequest(SeanceSearchRequest $searchRequest): AfficherSeancesRequest
    {
        $validated = $searchRequest->validated();

        return new AfficherSeancesRequest(
            recherche: $validated['recherche'] ?? null,
            filmId: null, // TODO: Gérer les ValueObjects avec UUID
            salleId: null, // TODO: Gérer les ValueObjects avec UUID
            etat: isset($validated['etat']) ? EtatSeance::from($validated['etat']) : null,
            qualiteProjection: isset($validated['qualite_projection']) ? QualiteProjection::from($validated['qualite_projection']) : null,
            dateDebut: isset($validated['date_debut']) ? CarbonImmutable::parse($validated['date_debut']) : null,
            dateFin: isset($validated['date_fin']) ? CarbonImmutable::parse($validated['date_fin']) : null,
            page: $validated['page']       ?? 1,
            perPage: $validated['perPage'] ?? 15,
        );
    }
}
