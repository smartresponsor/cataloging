<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use App\Cataloging\ValueObject\CategoryReadScopeRequest;

/**
 * Defines the contract for category read scope service.
 */
interface CatalogCategoryReadScopeServiceInterface
{
    public function applyTenantScope(CategoryReadScopeRequest $request): CategoryProjectionCriteria;
}
