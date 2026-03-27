<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\ServiceInterface\Governance\CategoryGovernanceViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

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
