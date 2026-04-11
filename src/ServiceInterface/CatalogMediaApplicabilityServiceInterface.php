<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Defines the contract for catalog media applicability service.
 */
interface CatalogMediaApplicabilityServiceInterface
{
    public function evaluate(CategoryEvaluationRequest $request): CategoryMediaApplicabilityEvaluatedInterface;
}
