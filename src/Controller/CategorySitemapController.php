<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategorySitemapController
{
    #[Route('/category-sitemap.xml', name: 'category_sitemap')]
    public function __invoke(): Response
    {
        $xml = '<urlset />';
        if (file_exists('public/category-sitemap.xml')) {
            $loaded = file_get_contents('public/category-sitemap.xml');
            if (false !== $loaded) {
                $xml = $loaded;
            }
        }

        return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
