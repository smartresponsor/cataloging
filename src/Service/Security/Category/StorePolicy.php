<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Security\Category;

final class StorePolicy
{
    public function evaluate(array $category, string $storeId): array
    {
        $visibility = $category['visibility'][$storeId] ?? 'visible';
        $priority = $category['priority'][$storeId] ?? 100;
        $row = [
            'id' => $category['id'] ?? null,
            'store_id' => $storeId,
            'visibility' => $visibility,
            'priority' => $priority,
        ];
        $all = [];
        if (is_file('report/catalog-store-policy.json')) {
            $all = json_decode(file_get_contents('report/catalog-store-policy.json'), true) or [];
        }
        $all[] = $row;
        file_put_contents('report/catalog-store-policy.json', json_encode($all, JSON_PRETTY_PRINT));

        return $row;
    }
}
