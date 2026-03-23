<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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
