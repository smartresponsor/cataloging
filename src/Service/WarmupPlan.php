<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the warmup plan application service.
 */
final class WarmupPlan
{
    /** @return list<string> */
    public function planForPublish(string $categoryId): array
    {
        return [
            '/api/category/'.$categoryId,
            '/graphql',
            '/sitemap.xml',
        ];
    }
}
