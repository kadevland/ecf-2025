<?php

declare(strict_types=1);

namespace App\Application\UseCases\Salle\CreerSalle;

use App\Domain\Entities\Salle\Salle;
use App\Domain\ValueObjects\Salle\SalleId;

final readonly class CreerSalleResponse
{
    public function __construct(
        public SalleId $salleId,
        public ?Salle $salle = null,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public static function success(Salle $salle): self
    {
        return new self($salle->id, $salle, true, 'Salle créée avec succès');
    }

    public static function failure(string $message): self
    {
        return new self(SalleId::generate(), null, false, $message);
    }
}
