<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryCollectionRequest;
use App\Response\ApiResponseBuilder;
use App\Service\CatalogVirtualCategoryService;
use App\Service\RuleValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryVirtualController
{
    public function __construct(
        private readonly CatalogVirtualCategoryService $service,
        private readonly ApiResponseBuilder $responseBuilder,
        private readonly RuleValidator $ruleValidator,
    ) {
    }

    #[Route('/api/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    public function preview(Request $request): JsonResponse
    {
        $input = CategoryCollectionRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            $payload = $this->responseBuilder->error('validation_failed', $input->getErrors(), 400);

            return new JsonResponse($payload, 400);
        }

        $ruleErrors = $this->ruleValidator->validate($input->rules);
        if ([] !== $ruleErrors) {
            $payload = $this->responseBuilder->error('validation_failed', $ruleErrors, 400);

            return new JsonResponse($payload, 400);
        }

        $result = $this->service->preview($input->rules);
        $payload = $this->responseBuilder->success([
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);

        return new JsonResponse($payload);
    }

    #[Route('/api/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function apply(string $id): JsonResponse
    {
        $result = $this->service->apply($id);
        if (null === $result) {
            $payload = $this->responseBuilder->error('not_found', ['not_found'], 404);

            return new JsonResponse($payload, 404);
        }

        $payload = $this->responseBuilder->success(['item' => $result]);

        return new JsonResponse($payload);
    }
}
