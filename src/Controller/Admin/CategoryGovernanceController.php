<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Admin;

use App\Cataloging\ServiceInterface\Governance\CategoryGovernanceViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category governance controller application flow.
 */
final readonly class CategoryGovernanceController
{
    /**
     * Initializes the category governance controller service collaborators.
     */
    public function __construct(private CategoryGovernanceViewBuilderInterface $viewBuilder)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/governance/{categoryId}', name: 'admin_category_governance', methods: ['GET'])]
    public function __invoke(Request $request, string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
