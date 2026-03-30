<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryRuntimeStatusController
{
    public function __construct(private readonly CategoryRuntimeStatusViewBuilderInterface $viewBuilder)
    {
    }

    #[Route('/admin/category/runtime-status/{categoryId}', name: 'admin_category_runtime_status', methods: ['GET'])]
    public function __invoke(string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
