<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Controller\Admin;

use App\ServiceInterface\Governance\CategoryGovernanceViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryGovernanceController
{
    public function __construct(private readonly CategoryGovernanceViewBuilderInterface $viewBuilder)
    {
    }

    #[Route('/admin/category/governance/{categoryId}', name: 'admin_category_governance', methods: ['GET'])]
    public function __invoke(Request $request, string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
