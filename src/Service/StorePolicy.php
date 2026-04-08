<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the store policy application service.
 */
final class StorePolicy
{
    /**
     * @param array{id?: scalar|null, visibility?: array<string, string>, priority?: array<string, int>} $category
     *
     * @return array{id: scalar|null, store_id: string, visibility: string, priority: int}
     */
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
        /** @var list<array{id: scalar|null, store_id: string, visibility: string, priority: int}> $all */
        $all = [];
        if (is_file('report/category-store-policy.json')) {
            $raw = file_get_contents('report/category-store-policy.json');
            if (false !== $raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    /** @var list<array{id: scalar|null, store_id: string, visibility: string, priority: int}> $all */
                    $all = $decoded;
                }
            }
        }
        $all[] = $row;
        file_put_contents('report/category-store-policy.json', json_encode($all, JSON_PRETTY_PRINT));

        return $row;
    }
}
