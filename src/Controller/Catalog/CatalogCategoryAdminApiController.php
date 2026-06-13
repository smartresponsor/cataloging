<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
/**
 * Admin API delivery adapter over shared catalog read services.
 */
final readonly class CatalogCategoryAdminApiController
{
    /**
     * Initializes the category admin api controller service collaborators.
     */
    public function __construct(private CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * Handles the list workflow.
     */
    #[Route('/api/catalog/category/admin/list', name: 'api_admin_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryProjectionReadService->list(CategoryProjectionCriteria::fromArray([
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
                'limit' => $request->query->get('limit') ?? 100,
                'offset' => $request->query->get('offset') ?? 0,
                'order' => $request->query->get('order') ?? 'updated_at',
                'direction' => $request->query->get('direction') ?? 'desc',
            ]));
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Unable to load admin category list.'], 500);
        }

        return new JsonResponse(['data' => $data]);
    }

    /**
     * Handles the bulk workflow.
     */
    #[Route('/api/catalog/category/bulk', name: 'api_admin_category_bulk', methods: ['POST'])]
    public function bulk(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return new JsonResponse(['ok' => false, 'error' => $exception->getMessage()], 400);
        }

        return new JsonResponse(['ok' => true, 'payload' => $payload]);
    }
}
