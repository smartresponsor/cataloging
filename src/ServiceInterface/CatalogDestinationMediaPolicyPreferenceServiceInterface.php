<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEventInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;

/**
 * Defines the contract for catalog destination media policy preference service.
 */
interface CatalogDestinationMediaPolicyPreferenceServiceInterface
{
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEventInterface;
}
