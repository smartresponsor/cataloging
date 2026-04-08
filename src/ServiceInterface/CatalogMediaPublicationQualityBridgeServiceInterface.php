<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
/**
 * Defines the contract for catalog media publication quality bridge service.
 */
interface CatalogMediaPublicationQualityBridgeServiceInterface
{
    /** @param array<string,mixed> $payload */
    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryPublicationQualityEvaluatedInterface;
}
