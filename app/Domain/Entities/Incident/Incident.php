<?php

declare(strict_types=1);

namespace App\Domain\Entities\Incident;

use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\PrioriteIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\TypeIncident;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Incident\IncidentId;
use App\Domain\ValueObjects\Salle\SalleId;
use App\Domain\ValueObjects\User\UserId;
use BadMethodCallException;
use Carbon\CarbonImmutable;
use DomainException;
use InvalidArgumentException;

/**
 * Entité Incident - Représente un incident technique
 */
final class Incident implements EntityInterface
{
    public function __construct(
        public private(set) IncidentId $id,
        public private(set) UserId $rapportePar,
        public private(set) CinemaId $cinemaId,
        public private(set) ?SalleId $salleId,
        public private(set) TypeIncident $type,
        public private(set) PrioriteIncident $priorite,
        public private(set) StatutIncident $statut,
        public private(set) string $titre,
        public private(set) string $description,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
        public private(set) ?UserId $assigneA,
        public private(set) ?CarbonImmutable $resolueAt,
        public private(set) ?string $solutionApportee,
        public private(set) ?string $commentairesInternes,
    ) {
        $this->enforceInvariants();
    }

    // === MAGIC METHODS ===

    public function __get(string $name): mixed
    {
        return match ($name) {
            'estOperationnel' => $this->statut !== StatutIncident::Ferme,
            'estEnAttente'    => $this->estOuvert(),
            'estActif'        => ! $this->estFerme(),
            default           => throw new BadMethodCallException("Property {$name} does not exist"),
        };
    }

    // === FACTORY METHODS ===

    public static function creer(
        IncidentId $id,
        UserId $rapportePar,
        CinemaId $cinemaId,
        ?SalleId $salleId,
        TypeIncident $type,
        PrioriteIncident $priorite,
        string $titre,
        string $description
    ): self {
        $now = CarbonImmutable::now();

        return new self(
            id: $id,
            rapportePar: $rapportePar,
            cinemaId: $cinemaId,
            salleId: $salleId,
            type: $type,
            priorite: $priorite,
            statut: StatutIncident::Ouvert,
            titre: $titre,
            description: $description,
            createdAt: $now,
            updatedAt: $now,
            assigneA: null,
            resolueAt: null,
            solutionApportee: null,
            commentairesInternes: null,
        );
    }

    // === BUSINESS METHODS ===

    public function assigner(UserId $utilisateurId): void
    {
        if ($this->statut === StatutIncident::Ferme) {
            throw new DomainException('Impossible d\'assigner un incident fermé');
        }

        $this->assigneA = $utilisateurId;

        if ($this->statut === StatutIncident::Ouvert) {
            $this->statut = StatutIncident::EnCours;
        }

        $this->touch();
    }

    public function desassigner(): void
    {
        if ($this->statut === StatutIncident::Ferme) {
            throw new DomainException('Impossible de désassigner un incident fermé');
        }

        $this->assigneA = null;

        if ($this->statut === StatutIncident::EnCours) {
            $this->statut = StatutIncident::Ouvert;
        }

        $this->touch();
    }

    public function resoudre(string $solution, ?string $commentaires = null): void
    {
        if ($this->statut === StatutIncident::Ferme) {
            throw new DomainException('Incident déjà fermé');
        }

        $this->statut               = StatutIncident::Resolu;
        $this->resolueAt            = CarbonImmutable::now();
        $this->solutionApportee     = $solution;
        $this->commentairesInternes = $commentaires;

        $this->touch();
    }

    public function fermer(?string $commentaires = null): void
    {
        if ($this->statut === StatutIncident::Ferme) {
            return;
        }

        $this->statut = StatutIncident::Ferme;

        if ($commentaires !== null) {
            $this->commentairesInternes = $commentaires;
        }

        $this->touch();
    }

    public function reouvrir(?string $raison = null): void
    {
        if ($this->statut === StatutIncident::Ouvert) {
            return;
        }

        $this->statut           = StatutIncident::Ouvert;
        $this->resolueAt        = null;
        $this->solutionApportee = null;

        if ($raison !== null) {
            $this->commentairesInternes = $raison;
        }

        $this->touch();
    }

    public function ajouterCommentaire(string $commentaire): void
    {
        $timestamp          = CarbonImmutable::now()->format('Y-m-d H:i:s');
        $nouveauCommentaire = "[{$timestamp}] {$commentaire}";

        if ($this->commentairesInternes === null) {
            $this->commentairesInternes = $nouveauCommentaire;
        } else {
            $this->commentairesInternes .= "\n".$nouveauCommentaire;
        }

        $this->touch();
    }

    // === QUERY METHODS ===

    public function estOuvert(): bool
    {
        return $this->statut === StatutIncident::Ouvert;
    }

    public function estEnCours(): bool
    {
        return $this->statut === StatutIncident::EnCours;
    }

    public function estResolu(): bool
    {
        return $this->statut === StatutIncident::Resolu;
    }

    public function estFerme(): bool
    {
        return $this->statut === StatutIncident::Ferme;
    }

    public function estAssigne(): bool
    {
        return $this->assigneA !== null;
    }

    public function estCritique(): bool
    {
        return $this->priorite === PrioriteIncident::Critique;
    }

    public function estHaute(): bool
    {
        return $this->priorite === PrioriteIncident::Elevee;
    }

    public function concereSalle(): bool
    {
        return $this->salleId !== null;
    }

    public function tempsResolution(): ?int
    {
        if ($this->resolueAt === null) {
            return null;
        }

        return (int) $this->resolueAt->diffInMinutes($this->createdAt);
    }

    public function equals(EntityInterface $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    // === PRIVATE METHODS ===

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }

    private function enforceInvariants(): void
    {
        if (empty(mb_trim($this->titre))) {
            throw new InvalidArgumentException('Le titre de l\'incident ne peut pas être vide');
        }

        if (empty(mb_trim($this->description))) {
            throw new InvalidArgumentException('La description de l\'incident ne peut pas être vide');
        }

        if (mb_strlen($this->titre) > 200) {
            throw new InvalidArgumentException('Le titre de l\'incident ne peut pas dépasser 200 caractères');
        }

        if (mb_strlen($this->description) > 2000) {
            throw new InvalidArgumentException('La description de l\'incident ne peut pas dépasser 2000 caractères');
        }
    }
}
