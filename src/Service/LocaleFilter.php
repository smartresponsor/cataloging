<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the locale filter application service.
 */
final class LocaleFilter
{
    /**
     * @param list<array<string,mixed>> $categories
     *
     * @return list<array<string,mixed>>
     */
    public function filter(array $categories, string $locale): array
    {
        return array_values(array_filter(
            $categories,
            static fn (array $c): bool => (($c['locale'] ?? 'en') === $locale)
        ));
    }
}
