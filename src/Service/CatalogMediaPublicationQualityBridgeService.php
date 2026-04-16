<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\ServiceInterface\CatalogMediaPublicationQualityBridgeServiceInterface;
use App\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\ValueObject\CategoryEvaluationRequest;
use App\ValueObject\CategoryPublicationQualityEvaluationRequest;
use App\ValueObject\CategoryPublicationQualityInput;

/**
 * Provides the catalog media publication quality bridge service application service.
 */
final readonly class CatalogMediaPublicationQualityBridgeService implements CatalogMediaPublicationQualityBridgeServiceInterface
{
    /**
     * Initializes the catalog media publication quality bridge service service collaborators.
     */
    public function __construct(
        private CatalogMediaCompletenessBridgeServiceInterface $completenessBridge,
        private CatalogPublicationQualityServiceInterface $publicationQualityService,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryEvaluationRequest $request): CategoryPublicationQualityEvaluatedInterface
    {
        $completenessPayload = $this->completenessBridge->evaluate($request)->payload();

        return $this->publicationQualityService->evaluate(
            new CategoryPublicationQualityEvaluationRequest(
                new CategoryPublicationQualityInput(
                    $request->categoryId(),
                    $this->scalarInt($completenessPayload['score'] ?? 0),
                    CategoryPayloadValueNormalizer::boolMap($completenessPayload['publicationChecks'] ?? null),
                    CategoryPayloadValueNormalizer::boolMap($completenessPayload['checks'] ?? null),
                ),
                $request->auditContext(),
            ),
        );
    }

    private function scalarInt(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
