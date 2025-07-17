<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Billet\Elements;

/**
 * Element de vue pour le formulaire de recherche de billets
 */
final readonly class BilletSearchElementView
{
    public function __construct(
        public string $recherche = '',
        public string $typeTarif = '',
        public ?bool $utilise = null,
        public int $perPage = 15,
    ) {}

    /**
     * Obtenir les options de pagination
     */
    public function perPageOptions(): array
    {
        return [
            15  => '15 par page',
            25  => '25 par page',
            50  => '50 par page',
            100 => '100 par page',
        ];
    }

    /**
     * Obtenir les options de type de tarif
     */
    public function typeTarifOptions(): array
    {
        return [
            ''         => 'Tous les tarifs',
            'plein'    => 'Plein tarif',
            'reduit'   => 'Tarif réduit',
            'etudiant' => 'Étudiant',
            'senior'   => 'Senior',
            'enfant'   => 'Enfant',
            'groupe'   => 'Groupe',
        ];
    }

    /**
     * Obtenir les options d'utilisation
     */
    public function utiliseOptions(): array
    {
        return [
            ''  => 'Tous les billets',
            '1' => 'Utilisés seulement',
            '0' => 'Non utilisés seulement',
        ];
    }

    /**
     * Obtenir la valeur d'utilisation sélectionnée
     */
    public function getUtiliseValue(): string
    {
        if ($this->utilise === true) {
            return '1';
        }
        if ($this->utilise === false) {
            return '0';
        }

        return '';
    }
}
