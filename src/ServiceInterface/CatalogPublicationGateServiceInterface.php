<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationGateEvaluatedInterface;
use App\ValueObject\CategoryPublicationGateAssertionRequest;
use App\ValueObject\CategoryPublicationGateEvaluationRequest;

/**
 * Defines the contract for catalog publication gate service.
 */
interface CatalogPublicationGateServiceInterface
{
    public function evaluate(CategoryPublicationGateEvaluationRequest $request): CategoryPublicationGateEvaluatedInterface;

    public function assertCanPublish(CategoryPublicationGateAssertionRequest $request): void;
}
