<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Query\Category;

final class FacetIndexBuilder
{
    public function build(array $category): array
    {
        // Normalize a category into a facet-friendly representation.
        return [
            'id' => $category['id'] ?? '',
            'slug' => $category['slug'] ?? '',
            'path' => $category['path'] ?? '',
            'locale' => $category['locale'] ?? 'en',
            'name' => $category['name'] ?? '',
        ];
    }
}
