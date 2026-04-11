<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\ValueObject\CategoryProjectionCriteria;
use App\ValueObject\CategoryReadScopeRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Storefront delivery adapter over shared projection/read scope services.
 */
final readonly class CategoryStorefrontController
{
    /**
     * Initializes the category storefront controller service collaborators.
     */
    public function __construct(
        private CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private CategoryReadScopeServiceInterface $categoryReadScopeService,
    ) {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/category/storefront', name: 'api_category_storefront', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = $this->categoryReadScopeService->applyTenantScope(new CategoryReadScopeRequest(
                $request,
                CategoryProjectionCriteria::fromArray([
                    'tenant' => $request->query->get('tenant'),
                    'locale' => $request->query->get('locale'),
                    'workflow_state' => $request->query->get('workflow_state'),
                    'published' => $request->query->get('published') ?? true,
                    'limit' => $request->query->get('limit') ?? 200,
                    'offset' => $request->query->get('offset') ?? 0,
                    'order' => $request->query->get('order') ?? 'name',
                    'direction' => $request->query->get('direction') ?? 'asc',
                ]),
            ));

            return new JsonResponse($this->categoryProjectionReadService->list($criteria));
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        }
    }
}
