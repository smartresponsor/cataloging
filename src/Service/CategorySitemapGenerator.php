<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategorySitemapGeneratorInterface;

final class CategorySitemapGenerator implements CategorySitemapGeneratorInterface
{
    public function generateIndex(int $batchSize): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        // Implementation should stream batches to avoid memory pressure.
        $xml[] = '</sitemapindex>';

        return implode('', $xml);
    }
}
