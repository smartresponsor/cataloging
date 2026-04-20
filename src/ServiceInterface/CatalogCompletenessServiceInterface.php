<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;

/**
 * Defines the contract for catalog completeness service.
 */
interface CatalogCompletenessServiceInterface
{
    public function evaluate(CategoryEvaluationRequest $request): CategoryCompletenessEvaluatedInterface;
}
