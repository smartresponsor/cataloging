<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryMediaBoundEventInterface;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;

/**
 * Defines the contract for catalog media governance service.
 */
interface CatalogMediaGovernanceServiceInterface
{
    public function bind(CategoryMediaBindRequest $request): CatalogCategoryMediaBoundEventInterface;
}
