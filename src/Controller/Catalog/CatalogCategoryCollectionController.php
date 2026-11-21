<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Request\CategoryCollectionRequest;
use App\Cataloging\Service\CatalogCollectionService;
use App\Cataloging\Service\CategoryCollectionRuleNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category collection controller application flow.
 */
final readonly class CatalogCategoryCollectionController
{
    /**
     * Initializes the category collection controller service collaborators.
     */
    public function __construct(
        private CatalogCollectionService $service,
        private CategoryCollectionRuleNormalizer $ruleNormalizer,
    ) {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/catalog/category/collection', name: 'api_category_collection', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $input = CategoryCollectionRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $input->getErrors()], 400);
        }

        $rules = $this->ruleNormalizer->normalize($input->rules);
        $result = $this->service->build($rules);

        return new JsonResponse([
            'ok' => true,
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);
    }
}
