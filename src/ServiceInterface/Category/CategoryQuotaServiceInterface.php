<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Category;

use App\Cataloging\ValueObject\CategoryQuotaAllowanceRequest;

/**
 * Defines the contract for category quota service.
 */
interface CategoryQuotaServiceInterface
{
    /**
     * Handles the allow workflow.
     */
    public function allow(CategoryQuotaAllowanceRequest $request): bool;
}
