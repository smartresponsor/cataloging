<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Query\Category;

final class Mapper
{
    public function map(array $input): array
    {
        return [
            'id' => $input['id'] ?? '',
            'parentId' => $input['parent_id'] ?? null,
            'slug' => $input['slug'] ?? ($input['handle'] ?? ''),
            'name' => $input['name'] ?? ($input['title'] ?? ''),
            'locale' => $input['locale'] ?? 'en',
        ];
    }
}
