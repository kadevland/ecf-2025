<?php

declare(strict_types=1);

namespace App\Domain\Collections;

use App\Domain\Entities\Billet\Billet;

/**
 * @extends Collection<Billet>
 */
final class BilletCollection extends Collection
{
    protected function validateItem(mixed $item): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $item instanceof Billet) {
            throw new InvalidArgumentException('BilletCollection ne peut contenir que des instances de Billet');
        }
    }

    /**
    //  * Ajouter un billet à la collection
    //  */
    // public function add(Billet $billet): void
    // {
    //     $this->items[] = $billet;
    // }

    // /**
    //  * Obtenir un billet par son index
    //  */
    // public function get(int $index): ?Billet
    // {
    //     return $this->items[$index] ?? null;
    // }

    // /**
    //  * Filtrer les billets utilisés
    //  */
    // public function utilises(): self
    // {
    //     $filtered = new self();
    //     foreach ($this->items as $billet) {
    //         /** @var Billet $billet */
    //         if ($billet->utilise) {
    //             $filtered->add($billet);
    //         }
    //     }

    //     return $filtered;
    // }

    // /**
    //  * Filtrer les billets non utilisés
    //  */
    // public function nonUtilises(): self
    // {
    //     $filtered = new self();
    //     foreach ($this->items as $billet) {
    //         /** @var Billet $billet */
    //         if (! $billet->utilise) {
    //             $filtered->add($billet);
    //         }
    //     }

    //     return $filtered;
    // }

    // /**
    //  * Obtenir la valeur totale des billets
    //  */
    // public function valeurTotale(): float
    // {
    //     $total = 0.0;
    //     foreach ($this->items as $billet) {
    //         /** @var Billet $billet */
    //         $total += $billet->prix->montant / 100; // Convertir centimes en euros
    //     }

    //     return $total;
    // }

    // /**
    //  * Grouper par séance
    //  */
    // public function grouperParSeance(): array
    // {
    //     $groupes = [];
    //     foreach ($this->items as $billet) {
    //         /** @var Billet $billet */
    //         $seanceId = $billet->seanceId->uuid;
    //         if (! isset($groupes[$seanceId])) {
    //             $groupes[$seanceId] = new self();
    //         }
    //         $groupes[$seanceId]->add($billet);
    //     }

    //     return $groupes;
    // }
}
