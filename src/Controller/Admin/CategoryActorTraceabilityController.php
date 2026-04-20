<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Admin;

use App\Cataloging\ServiceInterface\Traceability\CategoryActorTraceabilityViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category actor traceability controller application flow.
 */
final readonly class CategoryActorTraceabilityController
{
    /**
     * Initializes the category actor traceability controller service collaborators.
     */
    public function __construct(private CategoryActorTraceabilityViewBuilderInterface $viewBuilder)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/traceability/{categoryId}', name: 'admin_category_actor_traceability', methods: ['GET'])]
    public function __invoke(Request $request, string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
