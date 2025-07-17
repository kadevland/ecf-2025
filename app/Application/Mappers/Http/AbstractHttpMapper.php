<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class AbstractHttpMapper
{
    /**
     * Convertit une Request HTTP en objet DTO
     */
    abstract public function toDTO(Request $request): object;

    /**
     * Valide les données de la requête
     */
    abstract public function validate(Request $request): array;

    /**
     * Extrait et nettoie une valeur nullable
     */
    protected function nullableString(?string $value): ?string
    {
        return empty(mb_trim($value ?? '')) ? null : mb_trim($value);
    }

    /**
     * Convertit une chaîne en array via JSON ou explosion
     */
    protected function stringToArray(?string $value, string $separator = ','): array
    {
        if (empty($value)) {
            return [];
        }

        // Tenter JSON d'abord
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Sinon, exploser par séparateur
        return array_filter(array_map('trim', explode($separator, $value)));
    }

    /**
     * Convertit une valeur booléenne flexible
     */
    protected function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(mb_strtolower($value), ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Convertit en entier avec valeur par défaut
     */
    protected function toInt($value, int $default = 0): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Convertit en float avec valeur par défaut
     */
    protected function toFloat($value, float $default = 0.0): float
    {
        return is_numeric($value) ? (float) $value : $default;
    }

    /**
     * Valide et lance une exception si les règles ne passent pas
     */
    protected function validateRequest(Request $request, array $rules): array
    {
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
