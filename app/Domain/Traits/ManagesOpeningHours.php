<?php

declare(strict_types=1);

namespace App\Domain\Traits;

use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\OpeningHoursForDay;
use Spatie\OpeningHours\TimeRange;

trait ManagesOpeningHours
{
    /**
     * Extrait toutes les données d'un OpeningHours vers un array
     *
     * @return array{
     *     monday?: array<string>,
     *     tuesday?: array<string>,
     *     wednesday?: array<string>,
     *     thursday?: array<string>,
     *     friday?: array<string>,
     *     saturday?: array<string>,
     *     sunday?: array<string>,
     *     exceptions?: array<array<string>>,
     * }
     */
    private function extractOpeningHoursData(OpeningHours $openingHours): array
    {

        $regularHours = $openingHours->flatMap(
            static fn (OpeningHoursForDay $openingHoursForDay, string $day) => [
                $day => array_filter(['hours' => $openingHoursForDay->map(static fn (TimeRange $timeRange) => $timeRange->format()), 'data' => $openingHoursForDay->data]),
            ],
        );

        $exceptions = [
            'exceptions' => $openingHours->flatMapExceptions(
                static fn (OpeningHoursForDay $openingHoursForDay, string $date) => [
                    $date => array_filter(['hours' => $openingHoursForDay->map(static fn (TimeRange $timeRange) => $timeRange->format()), 'data' => $openingHoursForDay->data]),
                ],
            ),
        ];

        // @phpstan-ignore return.type
        return array_merge($regularHours, $exceptions);
    }

    /**
     * Merge avec nouvelles données horaires
     *
     * @param array{
     *     monday?: array<string>,
     *     tuesday?: array<string>,
     *     wednesday?: array<string>,
     *     thursday?: array<string>,
     *     friday?: array<string>,
     *     saturday?: array<string>,
     *     sunday?: array<string>,
     *     exceptions?: array<array<string>>,
     * } $newData
     */
    private function mergeOpeningHoursData(OpeningHours $openingHours, array $newData): OpeningHours
    {
        $existingData = $this->extractOpeningHoursData($openingHours);

        if (isset($newData['exceptions'])) {
            $existingData['exceptions'] = array_merge(
                $existingData['exceptions'] ?? [],
                (array) $newData['exceptions']
            );
            unset($newData['exceptions']);
        }

        $mergedData = array_merge($existingData, $newData);

        return OpeningHours::create($mergedData);
    }

    /**
     * Remove des données spécifiques (jours standards ou exceptions)
     *
     * @param  array<string>  $keysToRemove
     */
    private function removeFromOpeningHours(OpeningHours $openingHours, array $keysToRemove): OpeningHours
    {
        $data = $this->extractOpeningHoursData($openingHours);

        foreach ($keysToRemove as $key) {
            if ($this->isExceptionKey($key)) {
                // Exception : remove de $data['exceptions'][$key]
                unset($data['exceptions'][$key]);

                // Clean up si plus d'exceptions
                if (empty($data['exceptions'])) {
                    unset($data['exceptions']);
                }
            } else {
                // Standard day : remove $data[$key]
                unset($data[$key]);
            }
        }

        return OpeningHours::create($data);
    }

    /**
     * Détermine si une clé est une exception (format date) ou un jour standard
     */
    private function isExceptionKey(string $key): bool
    {
        // Format date YYYY-MM-DD ou MM-DD
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $key) || preg_match('/^\d{2}-\d{2}$/', $key);
    }
}
