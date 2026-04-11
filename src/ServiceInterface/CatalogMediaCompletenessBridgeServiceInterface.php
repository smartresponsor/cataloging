<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Defines the contract for catalog media completeness bridge service.
 */
interface CatalogMediaCompletenessBridgeServiceInterface
{
    public function evaluate(CategoryEvaluationRequest $request): CategoryCompletenessEvaluatedInterface;
}
