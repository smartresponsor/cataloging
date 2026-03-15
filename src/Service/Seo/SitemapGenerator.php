<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Seo;

final class SitemapGenerator
{
    /** @param array<int, array{id:string,slug:string,locale:string,updatedAt:?\DateTimeImmutable}> $items */
    public function build(string $host, array $items): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($items as $it) {
            $loc = rtrim($host, '/').'/'.$it['locale'].'/'.trim($it['slug'], '/');
            $lastmod = $it['updatedAt'] ? $it['updatedAt']->format('c') : null;
            $xml[] = '<url><loc>'.htmlspecialchars($loc, ENT_XML1).'</loc>'.($lastmod ? '<lastmod>'.$lastmod.'</lastmod>' : '').'</url>';
        }
        $xml[] = '</urlset>';

        return implode('', $xml);
    }
}
