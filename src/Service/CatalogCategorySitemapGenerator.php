<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CatalogCategorySitemapGeneratorInterface as CategorySitemapGeneratorInterface;

final class CatalogCategorySitemapGenerator implements CategorySitemapGeneratorInterface
{
    public function generateIndex(int $batchSize): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        $xml[] = '</sitemapindex>';

        return implode('', $xml);
    }
}
