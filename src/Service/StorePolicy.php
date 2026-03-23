<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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
        if (is_file('report/category-store-policy.json')) {
            $all = json_decode(file_get_contents('report/category-store-policy.json'), true) or [];
        }
        $all[] = $row;
        file_put_contents('report/category-store-policy.json', json_encode($all, JSON_PRETTY_PRINT));

        return $row;
    }
}
