<?php

declare(strict_types=1);

namespace App\Request\Support;

final class RequestValueNormalizer
{
    public static function trimmedStringOrDefault(mixed $value, string $default): string
    {
        if (!is_scalar($value)) {
            return $default;
        }

        $normalized = trim((string) $value);

        return '' !== $normalized ? $normalized : $default;
    }

    public static function optionalTrimmedString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' !== $normalized ? $normalized : null;
    }

    public static function boolFromMixed(mixed $value, bool $default = false): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_string($value) => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default,
            default => $default,
        };
    }

    public static function nullableBoolFromMixed(mixed $value): ?bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_string($value) => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
            default => null,
        };
    }
}
