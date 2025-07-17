<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum QualiteProjection: string
{
    case Standard   = 'standard';
    case HD4K       = '4k';
    case HDR        = 'hdr';
    case IMAX       = 'imax';
    case TroisD     = '3d';
    case QuatreDX   = '4dx';
    case DolbyAtmos = 'dolby_atmos';
    case ScreenX    = 'screenx';
    case ICE        = 'ice_immersive';

    /**
     * @return array<string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $qualite) {
            $options[$qualite->value] = $qualite->label();
        }

        return $options;
    }

    public function label(): string
    {
        return match ($this) {
            self::Standard   => 'Standard',
            self::HD4K       => '4K Ultra HD',
            self::HDR        => 'HDR',
            self::IMAX       => 'IMAX',
            self::TroisD     => '3D',
            self::QuatreDX   => '4DX',
            self::DolbyAtmos => 'Dolby Atmos',
            self::ScreenX    => 'ScreenX',
            self::ICE        => 'ICE Immersive',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Standard   => 'Projection numérique standard',
            self::HD4K       => 'Image ultra haute définition 4K',
            self::HDR        => 'High Dynamic Range - couleurs et contraste améliorés',
            self::IMAX       => 'Écran géant IMAX avec son immersif',
            self::TroisD     => 'Projection 3D avec lunettes',
            self::QuatreDX   => 'Expérience 4DX avec effets sensoriels',
            self::DolbyAtmos => 'Son surround Dolby Atmos',
            self::ScreenX    => 'Projection sur 270 degrés',
            self::ICE        => 'Expérience immersive complète',
        };
    }
}
