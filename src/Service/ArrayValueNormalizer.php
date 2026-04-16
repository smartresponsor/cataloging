<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Provides reusable array normalization helpers for command and service layers.
 */
final class ArrayValueNormalizer
{
    /**
     * @return list<array<string, mixed>>
     */
    public function stringKeyedRowList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $rows = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalized = [];
            foreach ($row as $key => $item) {
                if (!is_string($key)) {
                    continue;
                }

                $normalized[$key] = $item;
            }

            $rows[] = $normalized;
        }

        return $rows;
    }
}
