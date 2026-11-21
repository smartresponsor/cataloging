<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category media binding workflows.
 */
final readonly class CategoryMediaBindRequest
{
    public function __construct(
        private CatalogCategoryMediaBindingEntityScope $scope,
        private CatalogCategoryMediaBindingEntityState $state,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function scope(): CatalogCategoryMediaBindingEntityScope
    {
        return $this->scope;
    }

    public function state(): CatalogCategoryMediaBindingEntityState
    {
        return $this->state;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
