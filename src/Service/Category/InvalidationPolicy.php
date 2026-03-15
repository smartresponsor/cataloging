<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

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
