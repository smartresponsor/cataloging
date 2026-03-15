<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class CategorySitemapGenerator
{
    public function generate(string $locale = 'en'): array
    {
        return [
            ['loc' => '/'.$locale.'/category/root', 'changefreq' => 'daily'],
        ];
    }
}
