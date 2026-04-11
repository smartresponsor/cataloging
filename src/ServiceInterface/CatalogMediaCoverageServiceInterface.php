<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryMediaCoverageEvaluatedInterface;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Defines the contract for catalog media coverage service.
 */
interface CatalogMediaCoverageServiceInterface
{
    public function evaluate(CategoryEvaluationRequest $request): CategoryMediaCoverageEvaluatedInterface;
}
