<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

final class SitemapGenerator
{
    public function __construct(private readonly CanonicalResolver $canonicalResolver)
    {
    }

    public function generate(array $categories, string $locale): string
    {
        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];
        foreach ($categories as $cat) {
            if (!empty($cat['noindex'])) {
                continue;
            }
            $loc = $this->canonicalResolver->resolve($cat, $locale);
            $xml[] = '<url><loc>'.htmlspecialchars($loc, ENT_XML1).'</loc></url>';
        }
        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }
}
