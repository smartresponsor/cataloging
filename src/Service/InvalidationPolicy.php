<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class InvalidationPolicy
{
    public function touchOnPublish(string $categoryId): array
    {
        // Return affected cache keys/topics to invalidate.
        return [
            'cache:category:tree',
            'cache:category:item:'.$categoryId,
            'cache:menu:main',
        ];
    }

    public function touchOnMove(string $categoryId): array
    {
        return [
            'cache:category:tree',
            'cache:breadcrumb:'.$categoryId,
        ];
    }
}
