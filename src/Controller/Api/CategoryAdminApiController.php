<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Api;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
/**
 * Admin API delivery adapter over shared catalog read services.
 */
final readonly class CategoryAdminApiController
{
    /**
     * Initializes the category admin api controller service collaborators.
     */
    public function __construct(private CategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * Handles the list workflow.
     */
    #[Route('/api/admin/category/list', name: 'api_admin_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->categoryProjectionReadService->list([
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
                'limit' => $request->query->get('limit') ?? 100,
                'offset' => $request->query->get('offset') ?? 0,
                'order' => $request->query->get('order') ?? 'updated_at',
                'direction' => $request->query->get('direction') ?? 'desc',
            ]),
        ]);
    }

    /**
     * Handles the bulk workflow.
     */
    #[Route('/api/admin/category/bulk', name: 'api_admin_category_bulk', methods: ['POST'])]
    public function bulk(Request $request): JsonResponse
    {
        return new JsonResponse(['ok' => true, 'payload' => json_decode($request->getContent(), true)]);
    }
}
