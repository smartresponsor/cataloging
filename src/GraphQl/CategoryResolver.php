<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\GraphQl;

final class CategoryResolver
{
    /**
     * @param array<string, mixed> $category
     *
     * @return list<array<string, mixed>>
     */
    public function children(array $category): array
    {
        $children = $category['children'] ?? [];
        if (!is_array($children)) {
            return [];
        }

        $normalized = [];
        foreach ($children as $item) {
            if (is_array($item)) {
                /* @var array<string, mixed> $item */
                $normalized[] = $item;
            }
        }

        return $normalized;
    }
}
