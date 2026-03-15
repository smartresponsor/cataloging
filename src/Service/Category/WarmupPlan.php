<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Layer\Category;

final class WarmupPlan
{
    public function planForPublish(string $categoryId): array
    {
        return [
            '/api/category/'.$categoryId,
            '/graphql',
            '/sitemap.xml',
        ];
    }
}
