<?php

declare(strict_types=1);

namespace App\Domain\Entities\ComponentEntity;

interface ComponentEntity
{
    // Marker interface pour distinguer les composants des vraies Entities
    // Indique : "Je suis un composant d'Entity, pas une Entity indépendante"
}
