<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Emplacement;

use App\Domain\Enums\CategorieSiege;
use App\Domain\Enums\EtatEmplacement;
use App\Domain\Enums\TypeEmplacement;
use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;

final readonly class Emplacement extends ValueObject
{
    private const MAX_LIGNES = 50;

    private const MAX_COLONNES = 100;

    private function __construct(
        public NumeroEmplacement $numero,
        public int $ligne,
        public int $colonne,
        public TypeEmplacement $type,
        public EtatEmplacement $etat,
        public ?CategorieSiege $categorie
    ) {
        $this->enforceInvariants();
    }

    public static function siege(
        NumeroEmplacement $numero,
        int $ligne,
        int $colonne,
        CategorieSiege $categorie,
        EtatEmplacement $etat = EtatEmplacement::Disponible
    ): self {
        return new self($numero, $ligne, $colonne, TypeEmplacement::Siege, $etat, $categorie);
    }

    public static function vide(
        NumeroEmplacement $numero,
        int $ligne,
        int $colonne
    ): self {
        return new self($numero, $ligne, $colonne, TypeEmplacement::Vide, EtatEmplacement::Indisponible, null);
    }

    public function numero(): NumeroEmplacement
    {
        return $this->numero;
    }

    public function ligne(): int
    {
        return $this->ligne;
    }

    public function colonne(): int
    {
        return $this->colonne;
    }

    public function type(): TypeEmplacement
    {
        return $this->type;
    }

    public function etat(): EtatEmplacement
    {
        return $this->etat;
    }

    public function categorie(): ?CategorieSiege
    {
        return $this->categorie;
    }

    public function estSiege(): bool
    {
        return $this->type === TypeEmplacement::Siege;
    }

    public function estReservable(): bool
    {
        return $this->type === TypeEmplacement::Siege &&
            $this->etat === EtatEmplacement::Disponible;
    }

    public function changerEtat(EtatEmplacement $nouvelEtat): self
    {
        return new self($this->numero, $this->ligne, $this->colonne, $this->type, $nouvelEtat, $this->categorie);
    }

    public function marquerReserve(): self
    {
        if (! $this->estReservable()) {
            throw new InvalidArgumentException('Cet emplacement n\'est pas réservable');
        }

        return $this->changerEtat(EtatEmplacement::Reserve);
    }

    public function liberer(): self
    {
        if (! $this->estSiege()) {
            throw new InvalidArgumentException('Impossible de libérer un emplacement qui n\'est pas un siège');
        }

        return $this->changerEtat(EtatEmplacement::Disponible);
    }

    public function marquerHorsService(): self
    {
        if (! $this->estSiege()) {
            throw new InvalidArgumentException('Seuls les sièges peuvent être mis hors service');
        }

        return $this->changerEtat(EtatEmplacement::HorsService);
    }

    public function equals(self $other): bool
    {
        return $this->numero->equals($other->numero) &&
            $this->ligne === $other->ligne &&
            $this->colonne === $other->colonne &&
            $this->type === $other->type &&
            $this->etat === $other->etat &&
            $this->categorie === $other->categorie;
    }

    protected function enforceInvariants(): void
    {
        // Validation position
        if ($this->ligne < 1 || $this->ligne > self::MAX_LIGNES) {
            throw new InvalidArgumentException('Ligne invalide: doit être entre 1 et '.self::MAX_LIGNES);
        }

        if ($this->colonne < 1 || $this->colonne > self::MAX_COLONNES) {
            throw new InvalidArgumentException('Colonne invalide: doit être entre 1 et '.self::MAX_COLONNES);
        }

        // Validation cohérence type/catégorie
        if ($this->type === TypeEmplacement::Siege && $this->categorie === null) {
            throw new InvalidArgumentException('Un siège doit avoir une catégorie');
        }

        if ($this->type !== TypeEmplacement::Siege && $this->categorie !== null) {
            throw new InvalidArgumentException('Seuls les sièges peuvent avoir une catégorie');
        }

        // Validation cohérence état/type selon la logique métier
        if ($this->type === TypeEmplacement::Siege) {
            // Sièges: Disponible, Reserve, HorsService
            $etatsValidePourSiege = [EtatEmplacement::Disponible, EtatEmplacement::Reserve, EtatEmplacement::HorsService];
            if (! in_array($this->etat, $etatsValidePourSiege, true)) {
                throw new InvalidArgumentException('État invalide pour un siège');
            }
        } else {
            // Vide: toujours Indisponible
            if ($this->etat !== EtatEmplacement::Indisponible) {
                throw new InvalidArgumentException('Un emplacement vide doit être indisponible');
            }
        }
    }
}
