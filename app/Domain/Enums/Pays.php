<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum Pays: string
{
    case France   = 'FR';
    case Belgique = 'BE';

    public function label(): string
    {
        return match ($this) {
            self::France   => 'France',
            self::Belgique => 'Belgique',
        };
    }

    // === TÉLÉPHONE ===

    public function indicatifTelephone(): string
    {
        return match ($this) {
            self::France   => '+33',
            self::Belgique => '+32',
        };
    }

    /**
     * @return int[]
     */
    public function longueurTelephone(): array
    {
        return match ($this) {
            self::France   => [10],
            self::Belgique => [9, 10],
        };
    }

    public function prefixeNational(): string
    {
        return match ($this) {
            self::France   => '0',
            self::Belgique => '0',

        };
    }

    /**
     * @return string[]
     */
    public function prefixesMobiles(): array
    {
        return match ($this) {
            self::France   => ['06', '07'],
            self::Belgique => ['04'],
        };
    }

    // === ADRESSE ===

    public function formatCodePostal(): string
    {
        return match ($this) {
            self::France   => '\d{5}',     // 75001
            self::Belgique => '\d{4}',     // 1000
        };
    }

    /**
     * @return int[]
     */
    public function plageCodePostal(): array
    {
        return match ($this) {
            self::France   => [1000, 99999],
            self::Belgique => [1000, 9999],
        };
    }
}
