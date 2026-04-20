<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;

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
