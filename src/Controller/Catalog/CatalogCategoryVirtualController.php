<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Request\CategoryCollectionRequest;
use App\Cataloging\Service\CatalogVirtualCollectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category virtual controller application flow.
 */
final readonly class CatalogCategoryVirtualController
{
    /**
     * Initializes the category virtual controller service collaborators.
     */
    public function __construct(private CatalogVirtualCollectionService $service)
    {
    }

    /**
     * Handles the preview workflow.
     */
    #[Route('/api/catalog/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    public function preview(Request $request): JsonResponse
    {
        $input = CategoryCollectionRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $input->getErrors()], 400);
        }

        $result = $this->service->preview($input->rules);

        return new JsonResponse([
            'ok' => true,
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);
    }

    /**
     * Handles the apply workflow.
     */
    #[Route('/api/catalog/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    public function apply(string $id): JsonResponse
    {
        $result = $this->service->apply($id);
        if (null === $result) {
            return new JsonResponse(['ok' => false, 'error' => 'not_found'], 404);
        }

        return new JsonResponse([
            'ok' => true,
            'item' => $result,
        ]);
    }
}
