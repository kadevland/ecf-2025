<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Cinema\Cinema;
use App\Domain\Enums\Pays;
use InvalidArgumentException;

/**
 * @extends Collection<Cinema>
 */
final class CinemaCollection extends Collection
{
    public function findByNom(string $nom): ?Cinema
    {
        /** @var Cinema|null */
        return $this->find(fn (Cinema $cinema) => $cinema->nom === $nom);
    }

    public function filterByPays(Pays $pays): self
    {
        return $this->filter(fn (Cinema $cinema) => $cinema->adresse->pays === $pays);
    }

    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Cinema) {
            throw new InvalidArgumentException('CinemaCollection ne peut contenir que des instances de Cinema');
        }
    }
}
