<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Exception;
use Sqids\Sqids;

trait HasSqids
{
    /**
     * Find a model by its sqid
     */
    public static function findBySqid(string $sqid): ?static
    {
        $instance = new static;
        $sqids    = $instance->getSqidsInstance();

        try {
            $decoded = $sqids->decode($sqid);

            if (empty($decoded)) {
                return null;
            }

            return static::find($decoded[0]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the sqid for this model
     */
    public function getSqidAttribute(): string
    {
        return $this->getSqidsInstance()->encode([(int) $this->id]);
    }

    /**
     * Retrieve the model for a bound value
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Si c'est pour le champ sqid, utiliser notre méthode findBySqid
        if ($field === 'sqid') {
            return static::findBySqid($value);
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * Get the Sqids instance
     */
    protected function getSqidsInstance(): Sqids
    {
        // Utiliser une clé unique pour votre application
        // Vous pouvez la définir dans .env
        $alphabet  = config('app.sqids_alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $minLength = config('app.sqids_min_length', 6);

        return new Sqids($alphabet, $minLength);
    }
}
