<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\Enums\Pays;
use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;
use Respect\Validation\Validator as v;

final readonly class Telephone extends ValueObject
{
    public string $numero;

    public Pays $pays;

    private function __construct(
        string $numero,
        Pays $pays

    ) {
        $this->numero = self::normaliser($numero);
        $this->pays   = $pays;
        $this->enforceInvariants();
    }

    public static function francais(string $numero): self
    {
        return new self($numero, Pays::France);
    }

    public static function belge(string $numero): self
    {
        return new self($numero, Pays::Belgique);
    }

    public static function fromInternational(string $numero): self
    {
        $numero = mb_trim($numero);

        foreach (Pays::cases() as $pays) {
            if (str_starts_with($numero, $pays->indicatifTelephone())) {

                return new self(str_replace($pays->indicatifTelephone(), $pays->prefixeNational(), $numero), $pays);
            }
        }

        throw new InvalidArgumentException('Format international non supporté');
    }

    public function estMobile(): bool
    {
        $prefixes = $this->pays->prefixesMobiles();

        foreach ($prefixes as $prefixe) {
            if (str_starts_with($this->numero, $prefixe)) {
                // Vérification spéciale pour Belgique 04xx
                if ($this->pays === Pays::Belgique && $prefixe === '04') {
                    return mb_strlen($this->numero) === 10; // Les mobiles belges 04xx font 10 chiffres
                }

                return true;
            }
        }

        return false;
    }

    public function estFixe(): bool
    {
        return ! $this->estMobile();
    }

    public function equals(self $other): bool
    {
        return $this->numero === $other->numero &&
            $this->pays === $other->pays;
    }

    protected function enforceInvariants(): void
    {
        // Normalisation préalable effectuée
        $numero = $this->numero;
        $pays   = $this->pays;

        // Validation longueur selon le pays
        $paysInstance     = Pays::from($pays->value);
        $longueursValides = $paysInstance->longueurTelephone();

        $longueurActuelle = mb_strlen($numero);

        try {
            v::in($longueursValides)->assert($longueurActuelle);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new InvalidArgumentException(
                "Longueur invalide pour {$pays->label()}: {$longueurActuelle} chiffres. Attendu: ".implode(' ou ', $longueursValides)
            );
        }

        // Validation format de base
        try {
            v::startsWith('0')->digit()
                ->assert($numero);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new InvalidArgumentException(
                'Format invalide: doit commencer par 0 et contenir uniquement des chiffres'
            );
        }

        // Validation spécifique par pays
        match ($pays) {
            Pays::France   => $this->validerNumerofrenches($numero),
            Pays::Belgique => $this->validerNumeroBelge($numero),
        };
    }

    private static function normaliser(string $numero): string
    {
        // Retirer tous les caractères non numériques sauf +
        $numero = preg_replace('/[^\d+]/', '', $numero);

        if (empty($numero)) {
            throw new InvalidArgumentException('Numéro de téléphone vide après normalisation');
        }

        return $numero;
    }

    private function validerNumerofrenches(string $numero): void
    {
        // Préfixes valides France : 01-05 (fixe), 06-07 (mobile), 08-09 (services)
        try {
            v::regex('/^0[1-9]\d{8}$/')->assert($numero);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new InvalidArgumentException(
                'Numéro français invalide: doit être 0X XXXXXXXX avec X entre 1 et 9'
            );
        }
    }

    private function validerNumeroBelge(string $numero): void
    {
        if (mb_strlen($numero) === 9) {
            // Numéros fixes belges : 9 chiffres
            try {
                v::regex('/^0[1-9]\d{7}$/')->assert($numero);
            } catch (\Respect\Validation\Exceptions\ValidationException $e) {
                throw new InvalidArgumentException(
                    'Numéro belge fixe invalide: doit être 0X XXXXXXX avec X entre 1 et 9'
                );
            }
        } else {
            // Numéros mobiles belges : 10 chiffres commençant par 04
            try {
                v::regex('/^04\d{8}$/')->assert($numero);
            } catch (\Respect\Validation\Exceptions\ValidationException $e) {
                throw new InvalidArgumentException(
                    'Numéro belge mobile invalide: doit être 04 XXXXXXXX'
                );
            }
        }
    }
}
