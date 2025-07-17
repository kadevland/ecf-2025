<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasMapper;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasUseCase;
use App\Http\Controllers\Controller;
use CuyZ\Valinor\Mapper\MappingError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CinemaController extends Controller
{
    public function __construct(
        private readonly AfficherCinemasUseCase $afficherCinemasUseCase,
        private readonly AfficherCinemasMapper $mapper
    ) {}

    /**
     * Liste des cinémas avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Mapper HTTP → DTO avec validation Valinor
            $requestDto = $this->mapper->mapToRequest($request->all());

            // Exécuter le UseCase
            $response = $this->afficherCinemasUseCase->execute($requestDto);

            // Retourner response JSON
            return response()->json([
                'data' => $response->cinemas,
                'meta' => [
                    'pagination' => $response->pagination,
                    'criteria'   => $response->criteria,
                ],
            ]);

        } catch (MappingError $e) {
            // Erreurs de validation Valinor
            return response()->json([
                'error'   => 'Validation failed',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
