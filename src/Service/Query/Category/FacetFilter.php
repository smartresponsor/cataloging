<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Query\Category;

final class FacetFilter
{
    public function scope(array $row, ?string $pathPrefix, string $locale): bool
    {
        if ($row['locale'] !== $locale) {
            return false;
        }
        if ($pathPrefix && !str_starts_with($row['path'] ?? '', $pathPrefix)) {
            return false;
        }

        return true;
    }
}
