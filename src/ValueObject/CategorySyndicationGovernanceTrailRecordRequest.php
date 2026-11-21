<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for governance trail recording workflows.
 */
final readonly class CategorySyndicationGovernanceTrailRecordRequest
{
    public function __construct(
        private CategorySyndicationGovernanceTrailPayloadSet $payloadSet,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function payloadSet(): CategorySyndicationGovernanceTrailPayloadSet
    {
        return $this->payloadSet;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
