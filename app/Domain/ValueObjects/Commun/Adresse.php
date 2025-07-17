<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\Enums\Pays;
use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final readonly class Adresse extends ValueObject
{
    private function __construct(
        public string $rue,
        public string $codePostal,
        public string $ville,
        public Pays $pays
    ) {
        $this->enforceInvariants();
    }

    public static function francaise(string $rue, string $codePostal, string $ville): self
    {
        return new self(
            self::normaliserTexte($rue),
            self::normaliserCodePostal($codePostal),
            self::normaliserTexte($ville),
            Pays::France
        );
    }

    public static function belge(string $rue, string $codePostal, string $ville): self
    {
        return new self(
            self::normaliserTexte($rue),
            self::normaliserCodePostal($codePostal),
            self::normaliserTexte($ville),
            Pays::Belgique
        );
    }

    public function withRue(string $rue): self
    {
        return new self(
            self::normaliserTexte($rue),
            $this->codePostal,
            $this->ville,
            $this->pays
        );
    }

    public function withCodePostal(string $codePostal): self
    {
        return new self(
            $this->rue,
            self::normaliserCodePostal($codePostal),
            $this->ville,
            $this->pays
        );
    }

    public function withVille(string $ville): self
    {
        return new self(
            $this->rue,
            $this->codePostal,
            self::normaliserTexte($ville),
            $this->pays
        );
    }

    public function withPays(Pays $pays): self
    {
        return new self(
            $this->rue,
            $this->codePostal,
            $this->ville,
            $pays
        );
    }

    public function equals(self $other): bool
    {
        return $this->rue === $other->rue &&
            $this->codePostal === $other->codePostal &&
            $this->ville === $other->ville &&
            $this->pays === $other->pays;
    }

    protected function enforceInvariants(): void
    {
        // Validation rue
        try {
            v::stringType()->notEmpty()
                ->length(3, 255)
                ->assert($this->rue);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Rue invalide: doit contenir entre 3 et 255 caractères');
        }

        // Validation ville
        try {
            v::stringType()->notEmpty()
                ->length(2, 100)
                ->regex('/^[\p{L}\s\'-]+$/u')
                ->assert($this->ville);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Ville invalide: doit contenir entre 2 et 100 caractères et uniquement lettres, espaces, apostrophes et tirets');
        }

        // Validation code postal selon le pays
        match ($this->pays) {
            Pays::France   => $this->validerCodePostalFrancais(),
            Pays::Belgique => $this->validerCodePostalBelge(),
        };
    }

    private static function normaliserTexte(string $texte): string
    {
        // Supprimer espaces en début/fin et normaliser espaces multiples
        $texte = mb_trim($texte);
        $texte = preg_replace('/\s+/', ' ', $texte);

        if (empty($texte)) {
            throw new InvalidArgumentException('Le texte ne peut pas être vide après normalisation');
        }

        return $texte;
    }

    private static function normaliserCodePostal(string $codePostal): string
    {
        // Retirer tous les caractères non numériques
        $codePostal = preg_replace('/[^\d]/', '', $codePostal);

        if (empty($codePostal)) {
            throw new InvalidArgumentException('Code postal vide après normalisation');
        }

        return $codePostal;
    }

    private function validerCodePostalFrancais(): void
    {
        try {
            v::regex('/^\d{5}$/')->assert($this->codePostal);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Code postal français invalide: doit contenir exactement 5 chiffres');
        }

        // Validation plage française (01000-99999, exclut 00xxx)
        $code = (int) $this->codePostal;
        if ($code < 1000 || $code > 99999) {
            throw new InvalidArgumentException('Code postal français invalide: doit être entre 01000 et 99999');
        }
    }

    private function validerCodePostalBelge(): void
    {
        try {
            v::regex('/^\d{4}$/')->assert($this->codePostal);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Code postal belge invalide: doit contenir exactement 4 chiffres');
        }

        // Validation plage belge (1000-9999)
        $code = (int) $this->codePostal;
        if ($code < 1000 || $code > 9999) {
            throw new InvalidArgumentException('Code postal belge invalide: doit être entre 1000 et 9999');
        }
    }
}
