<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for publication quality evaluation workflows.
 */
final readonly class CategoryPublicationQualityEvaluationRequest
{
    /**
     * Initializes the category publication quality evaluation request value object.
     */
    public function __construct(
        private CategoryPublicationQualityInput $input,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function input(): CategoryPublicationQualityInput
    {
        return $this->input;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
