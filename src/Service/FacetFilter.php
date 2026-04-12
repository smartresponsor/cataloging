<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the facet filter application service.
 */
final class FacetFilter
{
    /** @param array{id?:mixed,slug?:mixed,name?:mixed,path?:mixed,locale?:mixed} $row */
    public function scope(array $row, ?string $pathPrefix, string $locale): bool
    {
        if (($row['locale'] ?? null) !== $locale) {
            return false;
        }
        $path = is_scalar($row['path'] ?? null) ? (string) $row['path'] : '';
        if (null !== $pathPrefix && '' !== $pathPrefix && !str_starts_with($path, $pathPrefix)) {
            return false;
        }

        return true;
    }
}
