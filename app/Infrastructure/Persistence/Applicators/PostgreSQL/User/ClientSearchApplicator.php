<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Applicators\PostgreSQL\User;

use App\Application\Conditions\ConditionInterface;
use App\Infrastructure\Persistence\Applicators\PostgreSQL\ConditionApplicatorInterface;
use App\Infrastructure\Persistence\Conditions\User\ConditionClientSearch;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

final class ClientSearchApplicator implements ConditionApplicatorInterface
{
    /**
     * @param  ConditionClientSearch  $condition
     */
    public function apply(Builder $query, ConditionInterface $condition): Builder
    {
        if (! $condition instanceof ConditionClientSearch) {
            throw new InvalidArgumentException('Expected ConditionClientSearch');
        }

        $search = $condition->search();

        return $query->where(function (Builder $q) use ($search) {
            // Recherche dans l'email
            $q->where('email', 'ILIKE', "%{$search}%")
              // Recherche dans le prénom du profil client
                ->orWhere('clients.first_name', 'ILIKE', "%{$search}%")
              // Recherche dans le nom du profil client
                ->orWhere('clients.last_name', 'ILIKE', "%{$search}%")
              // Recherche dans le téléphone du profil client
                ->orWhere('clients.phone', 'ILIKE', "%{$search}%");
        });
    }
}
