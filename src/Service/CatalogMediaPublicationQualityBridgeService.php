<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\Cataloging\ServiceInterface\CatalogMediaPublicationQualityBridgeServiceInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;

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
            $this->scalarInt($completenessPayload['score'] ?? 0),
            is_array($completenessPayload['publicationChecks'] ?? null) ? $completenessPayload['publicationChecks'] : [],
            is_array($completenessPayload['checks'] ?? null) ? $completenessPayload['checks'] : [],
            $actorId,
            $reason,
        );
    }

    private function scalarInt(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
