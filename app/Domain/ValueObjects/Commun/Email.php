<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\ValueObjects\ValueObject;
use Respect\Validation\Validator as v;

final readonly class Email extends ValueObject
{
    public function __construct(public string $value)
    {
        $this->enforceInvariants();
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function domain(): string
    {
        return mb_substr($this->value, mb_strpos($this->value, '@') + 1);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * {@inheritDoc}
     */
    protected function enforceInvariants(): void
    {
        v::email()->length(1, 320)->assert($this->value);
        v::domain()->assert($this->domain());
    }
}
