<?php

declare(strict_types=1);

namespace App\Casts;

use App\Domain\Enums\QualiteProjection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class QualitesProjectionCast implements CastsAttributes
{
    /**
     * Cast the given value to an array of QualiteProjection enums.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<QualiteProjection>|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null) {
            return null;
        }

        $decoded = is_string($value) ? json_decode($value, true) : $value;

        if (! is_array($decoded)) {
            return null;
        }

        return array_map(
            fn (string $qualite): QualiteProjection => QualiteProjection::from($qualite),
            $decoded
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<QualiteProjection>|array<string>|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_array($value)) {
            return null;
        }

        $values = array_map(
            fn (QualiteProjection|string $qualite): string => $qualite instanceof QualiteProjection ? $qualite->value : $qualite,
            $value
        );

        return json_encode($values);
    }
}
