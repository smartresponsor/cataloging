<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaReadinessEvaluatedEventInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;

/**
 * Defines the contract for catalog destination media readiness service.
 */
interface CatalogDestinationMediaReadinessServiceInterface
{
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CatalogCategoryDestinationMediaReadinessEvaluatedEventInterface;
}
