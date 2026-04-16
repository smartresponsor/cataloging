<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Normalizes category collection rule payloads into scalar-or-list maps.
 */
final class CategoryCollectionRuleNormalizer
{
    /**
     * @param array<string,mixed> $rules
     *
     * @return array<string, array<int, bool|float|int|string>|bool|float|int|string>
     */
    public function normalize(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $key => $value) {
            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                $normalized[$key] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            $items = [];
            foreach ($value as $item) {
                if (is_bool($item) || is_float($item) || is_int($item) || is_string($item)) {
                    $items[] = $item;
                }
            }
            $normalized[$key] = $items;
        }

        return $normalized;
    }
}
