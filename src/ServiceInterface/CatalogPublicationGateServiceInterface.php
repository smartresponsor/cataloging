<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryPublicationGateEvaluatedEventInterface;
use App\Cataloging\ValueObject\CategoryPublicationGateAssertionRequest;
use App\Cataloging\ValueObject\CategoryPublicationGateEvaluationRequest;

/**
 * Defines the contract for catalog publication gate service.
 */
interface CatalogPublicationGateServiceInterface
{
    public function evaluate(CategoryPublicationGateEvaluationRequest $request): CatalogCategoryPublicationGateEvaluatedEventInterface;

    public function assertCanPublish(CategoryPublicationGateAssertionRequest $request): void;
}
