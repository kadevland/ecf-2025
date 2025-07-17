<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;

final readonly class Prix extends ValueObject
{
    private Money $montant;

    private function __construct(Money $montant)
    {
        $this->montant = $montant;
    }

    public static function fromEuros(float $euros): self
    {
        $currency   = new Currency('EUR');
        $currencies = new ISOCurrencies();
        $divisor    = 10 ** $currencies->subunitFor($currency);

        $centimes = (int) round($euros * $divisor, 0, Money::ROUND_HALF_UP);

        return new self(new Money($centimes, $currency));
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromEuroCentimes(int $centimes): self
    {
        return new self(new Money($centimes, new Currency('EUR')));
    }

    public static function gratuit(): self
    {
        return new self(new Money(0, new Currency('EUR')));
    }

    public function getAmount(): int
    {
        return (int) $this->montant->getAmount();
    }

    public function getCurrency(): Currency
    {
        return $this->montant->getCurrency();
    }

    public function ajouter(self $autre): self
    {
        return new self($this->montant->add($autre->montant));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function soustraire(self $autre): self
    {
        $resultat = $this->montant->subtract($autre->montant);

        return new self($resultat);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function multiplier(float $facteur): self
    {
        return new self($this->montant->multiply((string) $facteur));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function appliquerReduction(int $pourcentage): self
    {
        if ($pourcentage < 0 || $pourcentage > 100) {
            throw new InvalidArgumentException('Discount percentage must be between 0 and 100');
        }

        return $this->multiplier((100 - $pourcentage) / 100);
    }

    public function avecTVA(float $taux): self
    {
        if ($taux < 0) {
            throw new InvalidArgumentException('TVA rate cannot be negative');
        }

        return $this->multiplier(1 + $taux);
    }

    /**
     * Retire la TVA du prix (TTC → HT)
     */
    public function sansTVA(float $taux): self
    {
        if ($taux < 0) {
            throw new InvalidArgumentException('TVA rate cannot be negative');
        }

        return new self($this->montant->divide((string) (1 + $taux)));
    }

    /**
     * Calcule le montant de TVA
     */
    public function montantTVA(float $taux): self
    {
        return $this->soustraire($this->sansTVA($taux));
    }

    public function estGratuit(): bool
    {
        return $this->montant->isZero();
    }

    public function isPositif(): bool
    {
        return ! $this->montant->isNegative();
    }

    public function isNegatif(): bool
    {
        return $this->montant->isNegative();
    }

    public function estSuperieur(self $autre): bool
    {
        return $this->montant->greaterThan($autre->montant);
    }

    public function estInferieur(self $autre): bool
    {
        return $this->montant->lessThan($autre->montant);
    }

    public function equals(self $autre): bool
    {
        return $this->montant->equals($autre->montant);
    }

    /**
     * {@inheritDoc}
     */
    protected function enforceInvariants(): void {}
}
