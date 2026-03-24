<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\ServiceInterface\CatalogMediaPublicationQualityBridgeServiceInterface;
use App\ServiceInterface\CatalogPublicationQualityServiceInterface;

final class CatalogMediaPublicationQualityBridgeService implements CatalogMediaPublicationQualityBridgeServiceInterface
{
    public function __construct(
        private readonly CatalogMediaCompletenessBridgeServiceInterface $completenessBridge,
        private readonly CatalogPublicationQualityServiceInterface $publicationQualityService,
    ) {
    }

    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryPublicationQualityEvaluatedInterface
    {
        $completenessPayload = $this->completenessBridge->evaluate($categoryId, $payload, $actorId, $reason)->payload();

        return $this->publicationQualityService->evaluate(
            $categoryId,
            (int) ($completenessPayload['score'] ?? 0),
            is_array($completenessPayload['publicationChecks'] ?? null) ? $completenessPayload['publicationChecks'] : [],
            is_array($completenessPayload['checks'] ?? null) ? $completenessPayload['checks'] : [],
            $actorId,
            $reason,
        );
    }
}
