<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
