<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryCollectionRequest;
use App\Response\ApiResponseBuilder;
use App\Service\CatalogCollectionService;
use App\Service\RuleNormalizer;
use App\Service\RuleValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryCollectionController
{
    public function __construct(
        private readonly CatalogCollectionService $service,
        private readonly ApiResponseBuilder $responseBuilder,
        private readonly RuleNormalizer $ruleNormalizer,
        private readonly RuleValidator $ruleValidator,
    ) {
    }

    #[Route('/api/category/collection', name: 'api_category_collection', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
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

        $result = $this->service->build($this->ruleNormalizer->normalize($input->rules));
        $payload = $this->responseBuilder->success([
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);

        return new JsonResponse($payload);
    }
}
