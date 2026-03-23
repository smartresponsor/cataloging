<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Controller\Admin;

use App\Service\Ops\CategoryRuntimeStatusViewBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryRuntimeStatusController
{
    public function __construct(private readonly CategoryRuntimeStatusViewBuilder $viewBuilder)
    {
    }

    #[Route('/admin/category/runtime-status/{categoryId}', name: 'admin_category_runtime_status', methods: ['GET'])]
    public function __invoke(string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
