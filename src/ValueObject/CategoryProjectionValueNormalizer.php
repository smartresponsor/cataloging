<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * Provides shared normalization helpers for category projection inputs.
 */
final class CategoryProjectionValueNormalizer
{
    public static function boolValue(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (!is_scalar($value)) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));
        if (in_array($normalized, ['1', 'true', 'yes'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no'], true)) {
            return false;
        }

        return null;
    }
}
