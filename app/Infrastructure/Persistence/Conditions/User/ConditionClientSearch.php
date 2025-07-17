<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Conditions\User;

use App\Application\Conditions\ConditionInterface;

/**
 * Condition pour rechercher dans les données spécifiques aux clients
 */
final readonly class ConditionClientSearch implements ConditionInterface
{
    private function __construct(
        private string $search
    ) {}

    public static function create(string $search): self
    {
        return new self($search);
    }

    public function search(): string
    {
        return $this->search;
    }

    public function type(): string
    {
        return 'client_search';
    }
}
