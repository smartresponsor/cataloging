<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\ValueObject\CategoryPublicationQualityEvaluationRequest;

/**
 * Defines the contract for catalog publication quality service.
 */
interface CatalogPublicationQualityServiceInterface
{
    /**
     * Evaluates publication quality for the provided request.
     */
    public function evaluate(
        CategoryPublicationQualityEvaluationRequest $request,
    ): CategoryPublicationQualityEvaluatedInterface;
}
