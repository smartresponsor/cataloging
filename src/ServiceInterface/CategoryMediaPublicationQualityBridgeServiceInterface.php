<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;

interface CategoryMediaPublicationQualityBridgeServiceInterface
{
    /** @param array<string,mixed> $payload */
    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryPublicationQualityEvaluatedInterface;
}
