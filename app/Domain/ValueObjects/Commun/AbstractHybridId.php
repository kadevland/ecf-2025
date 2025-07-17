<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\ValueObjects\ValueObject;
use Respect\Validation\Validator as v;

abstract readonly class AbstractHybridId extends ValueObject
{
    protected function __construct(
        public ?int $dbId,
        public string $uuid
    ) {
        $this->enforceInvariants();
    }

    /**
     * Crée un ID à partir des données de base de données
     */
    abstract public static function fromDatabase(int $dbId, string $uuid): static;

    /**
     * Génère un nouvel ID avec UUID seulement (avant persistence)
     */
    abstract public static function generate(): static;

    /**
     * Crée un ID à partir d'un UUID existant
     */
    abstract public static function fromUuid(string $uuid): static;

    /**
     * Indique si c'est une nouvelle entité (pas encore persistée)
     */
    final public function isNew(): bool
    {
        return $this->dbId === null;
    }

    /**
     * Vérifie l'égalité avec un autre ID du même type
     * Priorité : UUID (plus sûr pour l'égalité métier)
     */
    final public function equals(self $other): bool
    {
        return $this->uuid === $other->uuid;
    }

    /**
     * Vérifie l'égalité stricte (DB ID + UUID)
     */
    final public function strictEquals(self $other): bool
    {
        return $this->dbId === $other->dbId && $this->uuid === $other->uuid;
    }

    /**
     * {@inheritDoc}
     */
    protected function enforceInvariants(): void
    {
        // Validation UUID obligatoire
        v::uuid()->assert($this->uuid);

        // Validation DB ID si présent
        if ($this->dbId !== null) {
            v::intVal()->positive()
                ->assert($this->dbId);
        }
    }
}
