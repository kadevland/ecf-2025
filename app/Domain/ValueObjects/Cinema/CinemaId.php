<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Cinema;

use App\Domain\ValueObjects\Commun\AbstractHybridId;
use Ramsey\Uuid\Uuid;

/**
 * Identifiant hybride pour les cinémas
 *
 * Ce Value Object représente l'identifiant unique d'un cinéma dans le système.
 * Il utilise un système hybride combinant un ID numérique (pour la compatibilité
 * avec la base de données) et un UUID (pour l'unicité globale).
 *
 * @see AbstractHybridId Pour la logique de base des identifiants hybrides
 */
final readonly class CinemaId extends AbstractHybridId
{
    /**
     * Crée un CinemaId à partir des données stockées en base de données
     *
     * Cette méthode est utilisée lors de la récupération d'un cinéma existant
     * depuis la base de données, où l'ID numérique et l'UUID sont déjà définis.
     *
     * @param  int  $dbId  L'identifiant numérique en base de données
     * @param  string  $uuid  L'identifiant UUID associé
     * @return static Une nouvelle instance de CinemaId
     */
    public static function fromDatabase(int $dbId, string $uuid): static
    {
        return new self($dbId, $uuid);
    }

    /**
     * Génère un nouvel identifiant pour un cinéma
     *
     * Utilisé lors de la création d'un nouveau cinéma. L'ID numérique sera
     * assigné automatiquement par la base de données lors de l'insertion.
     *
     * @return static Une nouvelle instance avec un UUID v4 généré aléatoirement
     */
    public static function generate(): static
    {
        return new self(null, Uuid::uuid4()->toString());
    }

    /**
     * Crée un CinemaId à partir d'un UUID uniquement
     *
     * Utile pour les opérations où seul l'UUID est connu (par exemple,
     * lors de requêtes API ou de recherches par UUID).
     *
     * @param  string  $uuid  L'identifiant UUID du cinéma
     * @return static Une nouvelle instance sans ID numérique
     */
    public static function fromUuid(string $uuid): static
    {
        return new self(null, $uuid);
    }
}
