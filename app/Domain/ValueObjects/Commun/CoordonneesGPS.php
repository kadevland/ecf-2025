<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Commun;

use App\Domain\ValueObjects\ValueObject;
use InvalidArgumentException;

final readonly class CoordonneesGPS extends ValueObject
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        $this->enforceInvariants();
    }

    public static function create(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public static function fromArray(array $coordonnees): self
    {
        if (! isset($coordonnees['latitude'], $coordonnees['longitude'])) {
            throw new InvalidArgumentException('Array must contain latitude and longitude keys');
        }

        return new self(
            latitude: (float) $coordonnees['latitude'],
            longitude: (float) $coordonnees['longitude']
        );
    }

    public function equals(self $other): bool
    {
        return abs($this->latitude - $other->latitude) < 0.000001
            && abs($this->longitude - $other->longitude) < 0.000001;
    }

    protected function enforceInvariants(): void
    {
        if ($this->latitude < -90.0 || $this->latitude > 90.0) {
            throw new InvalidArgumentException(
                sprintf('Latitude must be between -90 and 90, got: %f', $this->latitude)
            );
        }

        if ($this->longitude < -180.0 || $this->longitude > 180.0) {
            throw new InvalidArgumentException(
                sprintf('Longitude must be between -180 and 180, got: %f', $this->longitude)
            );
        }
    }
}
