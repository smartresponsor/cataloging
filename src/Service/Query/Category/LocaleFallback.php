<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Query\Category;

final class LocaleFallback
{
    public function apply(array $categories, string $preferred, string $fallback = 'en'): array
    {
        $filtered = [];
        foreach ($categories as $c) {
            if (($c['locale'] ?? $fallback) === $preferred) {
                $filtered[] = $c;
            }
        }
        if ([] !== $filtered) {
            return $filtered;
        }
        foreach ($categories as $c) {
            if (($c['locale'] ?? $fallback) === $fallback) {
                $filtered[] = $c;
            }
        }

        return $filtered;
    }
}
