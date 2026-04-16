<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Provides reusable array normalization helpers for command and service layers.
 */
final class ArrayValueNormalizer
{
    /**
     * @return list<non-empty-string>
     */
    public function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        /** @var list<non-empty-string> $items */
        $items = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }

            $normalized = trim((string) $item);
            if ('' === $normalized) {
                continue;
            }

            $items[] = $normalized;
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function stringKeyedRowList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            /** @var array<string, mixed> $normalized */
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
