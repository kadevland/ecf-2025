<?php

declare(strict_types=1);

namespace App\Domain\Entities\Salle;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Salle\Components\OrganisationEmplacement;
use App\Domain\Enums\EtatSalle;
use App\Domain\Enums\QualiteProjection;
use App\Domain\Events\Salle\SalleCreatedEvent;
use App\Domain\Events\Salle\SalleEtatChangeEvent;
use App\Domain\Traits\RecordsDomainEvents;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Salle\NumeroSalle;
use App\Domain\ValueObjects\Salle\SalleId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class Salle implements EntityInterface
{
    use RecordsDomainEvents;

    public function __construct(
        public private(set) SalleId $id,
        public private(set) NumeroSalle $numero,
        public private(set) CinemaId $cinemaId,
        /**
         * @var array<QualiteProjection>
         */
        public private(set) array $qualitesProjectionSupportees,
        public private(set) EtatSalle $etat,
        public private(set) OrganisationEmplacement $organisationEmplacement,
        public private(set) bool $estAccessiblePMR,
        public private(set) ?string $equipements,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();
        $this->recordEvent(new SalleCreatedEvent($this->id, $this->numero, $this->cinemaId));
    }

    public function changerNumero(NumeroSalle $nouveauNumero): void
    {
        if ($this->numero->equals($nouveauNumero)) {
            return;
        }

        $this->numero = $nouveauNumero;
        $this->touch();
    }

    /**
     * @param  array<QualiteProjection>  $qualites
     */
    public function changerQualitesProjectionSupportees(array $qualites): void
    {
        if (empty($qualites)) {
            throw new InvalidArgumentException('Une salle doit supporter au moins une qualité de projection');
        }

        $this->qualitesProjectionSupportees = [];
        foreach ($qualites as $qualite) {
            $this->ajouterQualiteProjection($qualite);
        }
    }

    public function ajouterQualiteProjection(QualiteProjection $qualite): void
    {
        if (in_array($qualite, $this->qualitesProjectionSupportees, true)) {
            return;
        }

        $this->qualitesProjectionSupportees[] = $qualite;
        $this->touch();
    }

    public function supprimerQualiteProjection(QualiteProjection $qualite): void
    {
        $index = array_search($qualite, $this->qualitesProjectionSupportees, true);

        if ($index === false) {
            return;
        }

        array_splice($this->qualitesProjectionSupportees, (int) $index, 1);
        $this->touch();
    }

    public function changerEtat(EtatSalle $nouvelEtat): void
    {
        if ($this->etat === $nouvelEtat) {
            return;
        }

        $ancienEtat = $this->etat;
        $this->etat = $nouvelEtat;
        $this->recordEvent(new SalleEtatChangeEvent($this->id, $ancienEtat, $nouvelEtat));
        $this->touch();
    }

    public function rendreAccessiblePMR(): void
    {
        if ($this->estAccessiblePMR) {
            return;
        }

        $this->estAccessiblePMR = true;
        $this->touch();
    }

    public function rendreInaccessiblePMR(): void
    {
        if (! $this->estAccessiblePMR) {
            return;
        }

        $this->estAccessiblePMR = false;
        $this->touch();
    }

    public function changerEquipements(?string $equipements): void
    {
        if ($equipements !== null) {
            $this->validerEquipements($equipements);
        }

        if ($this->equipements === $equipements) {
            return;
        }

        $this->equipements = $equipements;
        $this->touch();
    }

    public function redimensionner(int $nbLignes, int $nbColonnes): void
    {
        $this->organisationEmplacement = OrganisationEmplacement::creer($nbLignes, $nbColonnes);
        $this->touch();
    }

    public function estOperationnelle(): bool
    {
        return $this->etat === EtatSalle::Active;
    }

    public function estDisponible(): bool
    {
        return $this->estOperationnelle() && $this->organisationEmplacement->compterSiegesDisponibles() > 0;
    }

    public function estComplete(): bool
    {
        return $this->organisationEmplacement->estComplet();
    }

    public function capaciteMaximale(): int
    {
        return $this->organisationEmplacement->compterSieges();
    }

    public function nombreSiegesDisponibles(): int
    {
        return $this->organisationEmplacement->compterSiegesDisponibles();
    }

    public function nombreSiegesPMR(): int
    {
        return $this->organisationEmplacement->compterSiegesPMR();
    }

    public function supporteQualiteProjection(QualiteProjection $qualite): bool
    {
        return in_array($qualite, $this->qualitesProjectionSupportees, true);
    }

    /**
     * @return array<QualiteProjection>
     */
    public function getQualitesProjectionSupportees(): array
    {
        return $this->qualitesProjectionSupportees;
    }

    public function possedeCinema(CinemaId $cinemaId): bool
    {
        return $this->cinemaId->equals($cinemaId);
    }

    public function equals(EntityInterface $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    private function enforceInvariants(): void
    {
        if (empty($this->qualitesProjectionSupportees)) {
            throw new InvalidArgumentException('Une salle doit supporter au moins une qualité de projection');
        }

        foreach ($this->qualitesProjectionSupportees as $qualite) {
            // @phpstan-ignore instanceof.alwaysTrue
            if (! ($qualite instanceof QualiteProjection)) {
                throw new InvalidArgumentException('Qualité de projection invalide');
            }
        }

        if ($this->equipements !== null) {
            $this->validerEquipements($this->equipements);
        }
    }

    private function validerEquipements(string $equipements): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 500)
                ->assert($equipements);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Équipements invalides: doit contenir entre 1 et 500 caractères');
        }
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
