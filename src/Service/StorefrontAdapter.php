<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class StorefrontAdapter
{
    public function adapt(array $tree): array
    {
        $out = [];
        foreach ($tree as $node) {
            if (!($node['published'] ?? true)) {
                continue;
            }
            $out[] = [
                'id' => $node['id'],
                'name' => $node['name'] ?? '',
                'slug' => $node['slug'] ?? '',
                'locale' => $node['locale'] ?? 'en',
            ];
        }

        return $out;
    }
}
