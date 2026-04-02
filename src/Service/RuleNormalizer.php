<?php

declare(strict_types=1);

namespace App\Service;

final class RuleNormalizer
{
    /**
     * @param array<mixed> $rules
     *
     * @return array<string, array<int, bool|float|int|string>|bool|float|int|string>
     */
    public function normalize(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
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
