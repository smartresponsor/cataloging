<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the locale fallback application service.
 */
final class LocaleFallback
{
    /**
     * @param list<array<string,mixed>> $categories
     *
     * @return list<array<string,mixed>>
     */
    public function apply(array $categories, string $preferred, string $fallback = 'en'): array
    {
        $filtered = [];
        foreach ($categories as $categoryRow) {
            if ($this->localeValue($categoryRow, $fallback) === $preferred) {
                $filtered[] = $categoryRow;
            }
        }
        if ([] !== $filtered) {
            return $filtered;
        }
        foreach ($categories as $categoryRow) {
            if ($this->localeValue($categoryRow, $fallback) === $fallback) {
                $filtered[] = $categoryRow;
            }
        }

        return $filtered;
    }

    /** @param array<string,mixed> $category */
    private function localeValue(array $category, string $fallback): string
    {
        $value = $category['locale'] ?? $fallback;

        return is_scalar($value) ? (string) $value : $fallback;
    }
}
