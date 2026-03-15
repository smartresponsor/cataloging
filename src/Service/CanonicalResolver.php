<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

final class CanonicalResolver
{
    public function resolve(array $category, string $locale): string
    {
        $slug = $category['slug'] ?? 'category';

        return '/'.$locale.'/category/'.$slug;
    }
}
