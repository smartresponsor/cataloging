<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Normalizes category metadata payload maps.
 */
final readonly class CatalogMetaPayloadNormalizerService
{
    /**
     * @return array<string,array<string,bool|float|int|string|null>|bool|float|int|string|null>
     */
    public function normalize(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey)) {
                continue;
            }

            if (is_array($entryValue)) {
                $nested = [];
                foreach ($entryValue as $nestedKey => $nestedValue) {
                    if (!is_string($nestedKey)) {
                        continue;
                    }

                    if (
                        is_bool($nestedValue)
                        || is_float($nestedValue)
                        || is_int($nestedValue)
                        || is_string($nestedValue)
                        || null === $nestedValue
                    ) {
                        $nested[$nestedKey] = $nestedValue;
                    }
                }

                $normalized[$entryKey] = $nested;
                continue;
            }

            if (
                is_bool($entryValue)
                || is_float($entryValue)
                || is_int($entryValue)
                || is_string($entryValue)
                || null === $entryValue
            ) {
                $normalized[$entryKey] = $entryValue;
            }
        }

        return $normalized;
    }
}
