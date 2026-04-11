<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryProjectionCriteria;
use App\ValueObject\CategoryReadScopeRequest;

/**
 * Defines the contract for category read scope service.
 */
interface CategoryReadScopeServiceInterface
{
    public function applyTenantScope(CategoryReadScopeRequest $request): CategoryProjectionCriteria;
}
