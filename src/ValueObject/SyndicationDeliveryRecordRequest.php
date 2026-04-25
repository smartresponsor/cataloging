<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for syndication delivery recording workflows.
 */
final readonly class CatalogSyndicationDeliveryRecordRequest
{
    /**
     * Initializes the category syndication delivery record request value object.
     */
    public function __construct(
        private CategorySyndicationDeliveryContext $context,
        private CategorySyndicationDeliveryAttempt $attempt,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function context(): CategorySyndicationDeliveryContext
    {
        return $this->context;
    }

    public function attempt(): CategorySyndicationDeliveryAttempt
    {
        return $this->attempt;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDeliveryRecordRequest', false)) {
    class_alias(CatalogSyndicationDeliveryRecordRequest::class, __NAMESPACE__.'\\SyndicationDeliveryRecordRequest');
}
