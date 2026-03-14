<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryStorefrontController
{
    #[Route('/api/catalog/storefront', name: 'api_category_storefront', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            ['id' => 1, 'name' => 'Root', 'slug' => 'root', 'parent' => null],
            ['id' => 2, 'name' => 'Electronics', 'slug' => 'electronics', 'parent' => 1],
        ]);
    }
}
