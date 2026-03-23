<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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
