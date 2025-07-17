<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Film;

use App\Domain\ValueObjects\Commun\AbstractHybridId;
use Ramsey\Uuid\Uuid;

/**
 * Identifiant hybride pour les films
 */
final readonly class FilmId extends AbstractHybridId
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
