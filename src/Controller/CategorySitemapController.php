<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategorySitemapController
{
    #[Route('/category-sitemap.xml', name: 'category_sitemap')]
    public function __invoke(): Response
    {
        $xml = file_exists('public/category-sitemap.xml') ? file_get_contents('public/category-sitemap.xml') : '<urlset />';

        return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
