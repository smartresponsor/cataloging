<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Defines the contract for catalog media publication quality bridge service.
 */
interface CatalogMediaPublicationQualityBridgeServiceInterface
{
    public function evaluate(CategoryEvaluationRequest $request): CategoryPublicationQualityEvaluatedInterface;
}
