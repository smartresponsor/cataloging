<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Storefront delivery adapter over shared projection/read scope services.
 */
final class CategoryStorefrontController
{
    /**
     * Initializes the category storefront controller service collaborators.
     */
    public function __construct(
        private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private readonly CategoryReadScopeServiceInterface $categoryReadScopeService,
    ) {
    }
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/category/storefront', name: 'api_category_storefront', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = [
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published') ?? true,
                'limit' => $request->query->get('limit') ?? 200,
                'offset' => $request->query->get('offset') ?? 0,
                'order' => $request->query->get('order') ?? 'name',
                'direction' => $request->query->get('direction') ?? 'asc',
            ];
            $criteria = $this->categoryReadScopeService->applyTenantScope($request, $criteria);

            return new JsonResponse($this->categoryProjectionReadService->list($criteria));
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        }
    }
}
