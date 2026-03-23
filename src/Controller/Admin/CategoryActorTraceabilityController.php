<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\ServiceInterface\Traceability\CategoryActorTraceabilityViewBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryActorTraceabilityController
{
    public function __construct(private readonly CategoryActorTraceabilityViewBuilderInterface $viewBuilder)
    {
    }

    #[Route('/admin/category/traceability/{categoryId}', name: 'admin_category_actor_traceability', methods: ['GET'])]
    public function __invoke(Request $request, string $categoryId): JsonResponse
    {
        return new JsonResponse($this->viewBuilder->build($categoryId)->toArray());
    }
}
