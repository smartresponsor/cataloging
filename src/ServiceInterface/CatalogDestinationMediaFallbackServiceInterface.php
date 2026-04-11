<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;

/**
 * Defines the contract for catalog destination media fallback service.
 */
interface CatalogDestinationMediaFallbackServiceInterface
{
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CategoryDestinationMediaFallbackEvaluatedInterface;
}
