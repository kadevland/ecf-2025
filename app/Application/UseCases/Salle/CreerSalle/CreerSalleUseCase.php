<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\CreerSalle;

use App\Domain\Contracts\Repositories\Salle\SalleRepositoryInterface;
use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Salle\SalleId;
use Exception;
use InvalidArgumentException;

final readonly class CreerSalleUseCase
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository
    ) {}

    public function execute(CreerSalleRequest $request): CreerSalleResponse
    {
        try {
            $this->validateRequest($request);

            $salle = new Salle(
                id: SalleId::generate(),
                cinemaId: $request->cinemaId,
                nom: $request->nom,
                capacite: $request->capacite,
                type: $request->type,
                equipements: $request->equipements                           ?? [],
                organisationEmplacements: $request->organisationEmplacements ?? [],
            );

            $savedSalle = $this->salleRepository->save($salle);

            return CreerSalleResponse::success($savedSalle);

        } catch (Exception $e) {
            return CreerSalleResponse::failure('Erreur lors de la création de la salle: '.$e->getMessage());
        }
    }

    private function validateRequest(CreerSalleRequest $request): void
    {
        if (empty(mb_trim($request->nom))) {
            throw new InvalidArgumentException('Le nom de la salle est obligatoire');
        }

        if ($request->capacite <= 0) {
            throw new InvalidArgumentException('La capacité doit être positive');
        }
    }
}
