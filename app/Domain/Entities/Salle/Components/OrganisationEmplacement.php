<?php

declare(strict_types=1);

namespace App\Domain\Entities\Salle\Components;

use App\Domain\Entities\ComponentEntity\ComponentEntity;
use App\Domain\Enums\CategorieSiege;
use App\Domain\Enums\EtatEmplacement;
use App\Domain\ValueObjects\Emplacement\Emplacement;
use App\Domain\ValueObjects\Emplacement\NumeroEmplacement;
use InvalidArgumentException;

final class OrganisationEmplacement implements ComponentEntity
{
    private const int MAX_LIGNES   = 50;

    private const int MAX_COLONNES = 100;

    /** @var array<int, array<int, Emplacement|null>> */
    private array $matriceEmplacements = [];

    private function __construct(
        private int $nbLignes,
        private int $nbColonnes
    ) {
        $this->validerDimensions($nbLignes, $nbColonnes);
        $this->initialiserMatrice();
    }

    public static function creer(int $nbLignes, int $nbColonnes): self
    {
        return new self($nbLignes, $nbColonnes);
    }

    public function nbLignes(): int
    {
        return $this->nbLignes;
    }

    public function nbColonnes(): int
    {
        return $this->nbColonnes;
    }

    public function definirEmplacement(int $ligne, int $colonne, Emplacement $emplacement): void
    {
        $this->validerPosition($ligne, $colonne);
        $this->matriceEmplacements[$ligne][$colonne] = $emplacement;
    }

    public function obtenirEmplacement(NumeroEmplacement $numero): ?Emplacement
    {
        foreach ($this->matriceEmplacements as $ligne) {
            foreach ($ligne as $emplacement) {
                if (
                    $emplacement && $emplacement->numero()
                        ->equals($numero)
                ) {
                    return $emplacement;
                }
            }
        }

        return null;
    }

    public function obtenirEmplacementParPosition(int $ligne, int $colonne): ?Emplacement
    {
        $this->validerPosition($ligne, $colonne);

        return $this->matriceEmplacements[$ligne][$colonne] ?? null;
    }

    public function supprimerEmplacement(int $ligne, int $colonne): void
    {
        $this->validerPosition($ligne, $colonne);
        $this->matriceEmplacements[$ligne][$colonne] = null;
    }

    public function changerEtatEmplacement(NumeroEmplacement $numero, EtatEmplacement $nouvelEtat): void
    {
        $emplacement = $this->obtenirEmplacement($numero);

        if ($emplacement === null) {
            throw new InvalidArgumentException("Emplacement non trouvé: {$numero->valeur()}");
        }

        $nouvelEmplacement = $emplacement->changerEtat($nouvelEtat);

        // Remplacer dans la matrice
        foreach ($this->matriceEmplacements as $ligneIndex => $ligne) {
            foreach ($ligne as $colonneIndex => $emp) {
                if (
                    $emp && $emp->numero()
                        ->equals($numero)
                ) {
                    $this->matriceEmplacements[$ligneIndex][$colonneIndex] = $nouvelEmplacement;

                    return;
                }
            }
        }
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirTousLesEmplacements(): array
    {
        $emplacements = [];

        foreach ($this->matriceEmplacements as $ligne) {
            foreach ($ligne as $emplacement) {
                if ($emplacement !== null) {
                    $emplacements[] = $emplacement;
                }
            }
        }

        return $emplacements;
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirSieges(): array
    {
        return array_filter(
            $this->obtenirTousLesEmplacements(),
            fn (Emplacement $emplacement) => $emplacement->estSiege()
        );
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirSiegesDisponibles(): array
    {
        return array_filter(
            $this->obtenirSieges(),
            fn (Emplacement $emplacement) => $emplacement->estReservable()
        );
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirSiegesReserves(): array
    {
        return array_filter(
            $this->obtenirSieges(),
            fn (Emplacement $emplacement) => $emplacement->etat() === EtatEmplacement::Reserve
        );
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirSiegesHorsService(): array
    {
        return array_filter(
            $this->obtenirSieges(),
            fn (Emplacement $emplacement) => $emplacement->etat() === EtatEmplacement::HorsService
        );
    }

    /**
     * @return array<Emplacement>
     */
    public function obtenirSiegesPMR(): array
    {
        return array_filter(
            $this->obtenirSieges(),
            fn (Emplacement $emplacement) => $emplacement->categorie() === CategorieSiege::PMR
        );
    }

    public function compterSieges(): int
    {
        return count($this->obtenirSieges());
    }

    public function compterSiegesDisponibles(): int
    {
        return count($this->obtenirSiegesDisponibles());
    }

    public function compterSiegesPMR(): int
    {
        return count($this->obtenirSiegesPMR());
    }

    public function compterSiegesReserves(): int
    {
        return count($this->obtenirSiegesReserves());
    }

    public function estComplet(): bool
    {
        return count($this->obtenirSiegesDisponibles()) === 0;
    }

    public function tauxOccupation(): float
    {
        $totalSieges = $this->compterSieges();

        if ($totalSieges === 0) {
            return 0.0;
        }

        $siegesReserves = $this->compterSiegesReserves();

        return ($siegesReserves / $totalSieges) * 100;
    }

    /**
     * @return array<int, array<int, Emplacement|null>>
     */
    public function obtenirMatrice(): array
    {
        return $this->matriceEmplacements;
    }

    private function validerDimensions(int $lignes, int $colonnes): void
    {
        if ($lignes < 1 || $lignes > self::MAX_LIGNES) {
            throw new InvalidArgumentException('Nombre de lignes invalide: doit être entre 1 et '.self::MAX_LIGNES);
        }

        if ($colonnes < 1 || $colonnes > self::MAX_COLONNES) {
            throw new InvalidArgumentException('Nombre de colonnes invalide: doit être entre 1 et '.self::MAX_COLONNES);
        }
    }

    private function validerPosition(int $ligne, int $colonne): void
    {
        if ($ligne < 0 || $ligne >= $this->nbLignes) {
            throw new InvalidArgumentException('Ligne invalide: doit être entre 0 et '.($this->nbLignes - 1));
        }

        if ($colonne < 0 || $colonne >= $this->nbColonnes) {
            throw new InvalidArgumentException('Colonne invalide: doit être entre 0 et '.($this->nbColonnes - 1));
        }
    }

    private function initialiserMatrice(): void
    {
        for ($ligne = 0; $ligne < $this->nbLignes; $ligne++) {
            for ($colonne = 0; $colonne < $this->nbColonnes; $colonne++) {
                $this->matriceEmplacements[$ligne][$colonne] = null;
            }
        }
    }
}
