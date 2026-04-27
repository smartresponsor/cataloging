<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryCanonicalResolveRequest;

/**
 * Provides the sitemap generator application service.
 */
final readonly class CatalogSitemapGeneratorService
{
    /**
     * Initializes the sitemap generator service collaborators.
     */
    public function __construct(private CatalogCanonicalResolverService $canonicalResolver)
    {
    }

    /** @param list<array<string,mixed>> $categories */
    public function generate(array $categories, string $locale): string
    {
        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];
        foreach ($categories as $category) {
            if (!empty($category['noindex'])) {
                continue;
            }
            $location = $this->canonicalResolver->resolve(new CategoryCanonicalResolveRequest($category, $locale));
            $xml[] = '<url><loc>'.htmlspecialchars($location, ENT_XML1).'</loc></url>';
        }
        $xml[] = '</urlset>';

        return implode('
', $xml);
    }
}
