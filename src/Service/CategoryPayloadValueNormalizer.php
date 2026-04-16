<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Provides shared payload scalar and collection normalization helpers.
 */
final class CategoryPayloadValueNormalizer
{
    public static function scalarString(mixed $value, string $default = ''): string
    {
        if (!is_scalar($value)) {
            return $default;
        }

        $normalized = trim((string) $value);

        return '' !== $normalized ? $normalized : $default;
    }

    public static function nonEmptyString(mixed $value, string $default = ''): string
    {
        $normalized = self::scalarString($value);

        return '' !== $normalized ? $normalized : $default;
    }

    /** @return list<string> */
    public static function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            $normalized = self::scalarString($item);
            if ('' !== $normalized) {
                $items[] = $normalized;
            }
        }

        return $items;
    }

    /** @return array<string,string> */
    public static function stringMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $key => $item) {
            $normalizedKey = self::scalarString($key);
            if ('' === $normalizedKey || !is_scalar($item)) {
                continue;
            }

            $items[$normalizedKey] = trim((string) $item);
        }

        return $items;
    }

    /** @return array<string,mixed> */
    public static function nestedMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $key => $item) {
            $normalizedKey = self::scalarString($key);
            if ('' === $normalizedKey) {
                continue;
            }

            $items[$normalizedKey] = $item;
        }

        return $items;
    }

    /** @return array<string,bool> */
    public static function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $key => $item) {
            $normalizedKey = self::scalarString($key);
            if ('' === $normalizedKey) {
                continue;
            }

            $items[$normalizedKey] = (bool) $item;
        }

        return $items;
    }

    public static function intValue(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
