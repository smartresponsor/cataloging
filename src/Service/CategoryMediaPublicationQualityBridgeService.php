<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\ServiceInterface\CategoryMediaCompletenessBridgeServiceInterface;
use App\ServiceInterface\CategoryMediaPublicationQualityBridgeServiceInterface;
use App\ServiceInterface\CategoryPublicationQualityServiceInterface;

final class CategoryMediaPublicationQualityBridgeService implements CategoryMediaPublicationQualityBridgeServiceInterface
{
    public function __construct(
        private readonly CategoryMediaCompletenessBridgeServiceInterface $completenessBridge,
        private readonly CategoryPublicationQualityServiceInterface $publicationQualityService,
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
