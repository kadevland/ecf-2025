<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Salle;

use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final readonly class NumeroSalle extends ValueObject
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
        try {
            $validator = v::stringType()->notEmpty()
                ->length(1, 20)
                ->regex('/^[A-Z0-9\-_]+$/');
            $validator->assert($this->valeur);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Numéro de salle invalide: doit contenir entre 1 et 20 caractères alphanumériques majuscules, tirets et underscores');
        }
    }

    private static function normaliser(string $numero): string
    {
        $numero = mb_strtoupper(mb_trim($numero));

        if (empty($numero)) {
            throw new InvalidArgumentException('Numéro de salle vide après normalisation');
        }

        return $numero;
    }
}
