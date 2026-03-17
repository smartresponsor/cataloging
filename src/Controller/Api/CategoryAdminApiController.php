<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryAdminApiController
{
    #[Route('/api/admin/category/list', name: 'api_admin_category_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse([
            ['id' => 1, 'name' => 'Root'],
            ['id' => 2, 'name' => 'Electronics'],
        ]);
    }

    #[Route('/api/admin/category/bulk', name: 'api_admin_category_bulk', methods: ['POST'])]
    public function bulk(Request $request): JsonResponse
    {
        return new JsonResponse(['ok' => true, 'payload' => json_decode($request->getContent(), true)]);
    }
}
