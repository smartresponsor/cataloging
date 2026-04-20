<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Admin;

use App\Cataloging\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category runtime status controller application flow.
 */
final readonly class CategoryRuntimeStatusController
{
    /**
     * Initializes the category runtime status controller service collaborators.
     */
    public function __construct(private CategoryRuntimeStatusViewBuilderInterface $viewBuilder)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/runtime-status/{categoryId}', name: 'admin_category_runtime_status', methods: ['GET'])]
    public function __invoke(string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
