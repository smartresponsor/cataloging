<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class SeoPerLocale
{
    public function build(array $category): array
    {
        $locale = $category['locale'] ?? 'en';
        $title = 'uk' === $locale ? 'Категорія: '.$category['name'] : 'Category: '.$category['name'];
        $canonical = 'https://example.com/category/'.$category['slug'].'?lang='.$locale;
        $data = [
            'id' => $category['id'] ?? null,
            'locale' => $locale,
            'title' => $title,
            'description' => $title.' description',
            'canonical' => $canonical,
        ];
        file_put_contents('report/category-seo-locale.json', json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }
}
