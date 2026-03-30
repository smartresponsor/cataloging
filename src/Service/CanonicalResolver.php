<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CanonicalResolver
{
    /** @param array<string,mixed> $category */
    public function resolve(array $category, string $locale): string
    {
        $slug = $category['slug'] ?? 'category';

        return '/'.$locale.'/category/'.(is_scalar($slug) ? (string) $slug : 'category');
    }
}
