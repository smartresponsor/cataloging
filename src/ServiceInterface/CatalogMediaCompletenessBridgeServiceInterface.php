<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryCompletenessEvaluatedInterface;
/**
 * Defines the contract for catalog media completeness bridge service.
 */
interface CatalogMediaCompletenessBridgeServiceInterface
{
    /** @param array<string,mixed> $payload */
    public function evaluate(
        string $categoryId,
        array $payload,
        string $actorId,
        string $reason,
    ): CategoryCompletenessEvaluatedInterface;
}
