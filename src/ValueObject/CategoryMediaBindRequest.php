<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for category media binding workflows.
 */
final readonly class CategoryMediaBindRequest
{
    public function __construct(
        private CategoryMediaBindingScope $scope,
        private CategoryMediaBindingState $state,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function scope(): CategoryMediaBindingScope
    {
        return $this->scope;
    }

    public function state(): CategoryMediaBindingState
    {
        return $this->state;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
