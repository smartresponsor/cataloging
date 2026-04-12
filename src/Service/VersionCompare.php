<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the version compare application service.
 */
final readonly class VersionCompare
{
    /**
     * @param array<string,scalar|null> $fromValues
     * @param array<string,scalar|null> $toValues
     *
     * @return array<string, array{from: scalar|null, to: scalar|null}>
     */
    public function diff(array $fromValues, array $toValues): array
    {
        $result = [];
        foreach ($fromValues as $fieldName => $fromValue) {
            if (!array_key_exists($fieldName, $toValues) || $toValues[$fieldName] !== $fromValue) {
                $result[$fieldName] = ['from' => $fromValue, 'to' => $toValues[$fieldName] ?? null];
            }
        }
        foreach ($toValues as $fieldName => $toValue) {
            if (!array_key_exists($fieldName, $fromValues)) {
                $result[$fieldName] = ['from' => null, 'to' => $toValue];
            }
        }

        return $result;
    }
}
