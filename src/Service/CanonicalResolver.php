<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CanonicalResolver
{
    public function resolve(array $category, string $locale): string
    {
        $slug = $category['slug'] ?? 'category';

        return '/'.$locale.'/category/'.$slug;
    }
}
