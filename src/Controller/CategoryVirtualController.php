<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryCollectionRequest;
use App\Service\CatalogVirtualCategoryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryVirtualController
{
    public function __construct(private readonly CatalogVirtualCategoryService $service)
    {
    }

    #[Route('/api/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    public function preview(Request $request): JsonResponse
    {
        $input = CategoryCollectionRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'error' => 'validation_failed', 'errors' => $input->getErrors()], 400);
        }

        $result = $this->service->preview($input->rules);

        return new JsonResponse([
            'ok' => true,
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);
    }

    #[Route('/api/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    public function apply(string $id): JsonResponse
    {
        $result = $this->service->apply($id);
        if (null === $result) {
            return new JsonResponse(['ok' => false, 'error' => 'not_found', 'errors' => ['not_found']], 404);
        }

        return new JsonResponse([
            'ok' => true,
            'item' => $result,
        ]);
    }
}
