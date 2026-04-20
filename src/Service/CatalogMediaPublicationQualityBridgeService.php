<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\Cataloging\ServiceInterface\CatalogMediaPublicationQualityBridgeServiceInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationQualityEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationQualityInput;

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
