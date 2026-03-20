<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\GraphQl;

final class CategoryType
{
    public static function config(): array
    {
        return [
            'name' => 'Category',
            'fields' => [
                'id' => ['type' => 'ID'],
                'name' => ['type' => 'String'],
                'slug' => ['type' => 'String'],
                'locale' => ['type' => 'String'],
                'published' => ['type' => 'Boolean'],
            ],
        ];
    }
}
