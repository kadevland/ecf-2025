<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

abstract readonly class ValueObject
{
    /**
     * Vérifie et valide les invariants du Value Object en utilisant Respect\Validation.
     *
     * Cette méthode doit être implémentée par chaque Value Object concret pour valider
     * ses règles métier et contraintes d'intégrité avec les validateurs Respect.
     * Elle est appelée dans le constructeur pour garantir qu'aucun Value Object
     * invalide ne puisse être instancié.
     *
     * Les invariants sont des conditions qui doivent toujours être vraies pour que
     * l'objet soit dans un état valide (ex: email valide, prix positif, dimensions cohérentes).
     *
     * @throws \Respect\Validation\Exceptions\ValidationException Si une règle de validation échoue
     * @throws \App\Domain\Exceptions\DomainException Pour les erreurs métier spécifiques
     */
    abstract protected function enforceInvariants(): void;
}
