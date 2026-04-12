<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides shared normalization helpers for category destination media payloads.
 */
final class CategoryMediaInputNormalizer
{
    private function __construct()
    {
    }

    public static function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    public static function stringList(mixed $value): array
    {
        if (is_array($value)) {
            $items = $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/\s*,\s*/', $value) ?: [];
            }
        } elseif (is_scalar($value)) {
            $items = [(string) $value];
        } else {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' === $normalized) {
                continue;
            }
            $result[] = $normalized;
        }

        return array_values(array_unique($result));
    }

    /** @return array<string,bool> */
    public static function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                continue;
            }
            $normalizedKey = trim($key);
            if ('' === $normalizedKey) {
                continue;
            }
            $result[$normalizedKey] = (bool) $item;
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $settings
     *
     * @return array{channel:string,locale:string,requiredRoles:list<string>}
     */
    public static function destinationPayload(array $settings): array
    {
        return [
            'channel' => self::stringValue($settings['channel'] ?? null),
            'locale' => self::stringValue($settings['locale'] ?? null),
            'requiredRoles' => self::stringList($settings['requiredMediaRoles'] ?? null),
        ];
    }
}
