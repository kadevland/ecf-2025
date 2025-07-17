<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Reservation;

use App\Domain\ValueObjects\Commun\AbstractHybridId;
use Ramsey\Uuid\Uuid;

/**
 * Identifiant hybride pour les réservations
 */
final readonly class ReservationId extends AbstractHybridId
{
    public static function fromDatabase(int $dbId, string $uuid): static
    {
        return new self($dbId, $uuid);
    }

    public static function generate(): static
    {
        return new self(null, Uuid::uuid4()->toString());
    }

    public static function fromUuid(string $uuid): static
    {
        return new self(null, $uuid);
    }
}
