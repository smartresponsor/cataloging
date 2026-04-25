<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CatalogCategoryLinkEntityRequest;
use App\Cataloging\ValueObject\CategoryCreateRequest;
use App\Cataloging\ValueObject\CategoryResolveRequest;
use App\Cataloging\ValueObject\CategoryServiceMoveRequest;

/**
 * Defines the contract for category service.
 */
interface CategoryServiceInterface
{
    /** @return array<string,mixed> */
    public function create(CategoryCreateRequest $request): array;

    /** @return array<string,mixed> */
    public function move(CategoryServiceMoveRequest $request): array;

    /**
     * Handles the attach workflow.
     */
    public function attach(CatalogCategoryLinkEntityRequest $request): void;

    /**
     * Handles the detach workflow.
     */
    public function detach(CatalogCategoryLinkEntityRequest $request): void;

    /** @return list<array<string,mixed>> */
    public function resolve(CategoryResolveRequest $request): array;
}
