<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Emplacement;

use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final readonly class NumeroEmplacement extends ValueObject
{
    private function __construct(
        public string $valeur
    ) {
        $this->enforceInvariants();
    }

    public static function fromString(string $numero): self
    {
        return new self(self::normaliser($numero));
    }

    public function valeur(): string
    {
        return $this->valeur;
    }

    public function equals(self $other): bool
    {
        return $this->valeur === $other->valeur;
    }

    protected function enforceInvariants(): void
    {
        // Validation format de base uniquement
        try {
            $validator = v::stringType()->notEmpty()
                ->length(2, 10)
                ->regex('/^[A-Z0-9]+$/');
            $validator->assert($this->valeur);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Numéro d\'emplacement invalide: doit contenir entre 2 et 10 caractères alphanumériques majuscules');
        }
    }

    private static function normaliser(string $numero): string
    {
        // Supprimer espaces et convertir en majuscules
        $numero = mb_strtoupper(mb_trim($numero));

        if (empty($numero)) {
            throw new InvalidArgumentException('Numéro d\'emplacement vide après normalisation');
        }

        // Padding zéro pour les sièges si nécessaire
        if (preg_match('/^([A-Z])(\d{1})$/', $numero, $matches)) {
            // A5 → A05
            $numero = $matches[1].mb_str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        }

        return $numero;
    }
}
