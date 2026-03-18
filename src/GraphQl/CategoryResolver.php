<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\GraphQl;

final class CategoryResolver
{
    public function children(array $category): array
    {
        return $category['children'] ?? [];
    }
}
