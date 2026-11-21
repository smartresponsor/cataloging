<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for syndication destination registration workflows.
 */
final readonly class CatalogSyndicationDestinationRegisterRequest
{
    public function __construct(
        private CatalogSyndicationDestinationDefinition $definition,
        private CatalogSyndicationDestinationConfiguration $configuration,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function definition(): CatalogSyndicationDestinationDefinition
    {
        return $this->definition;
    }

    public function configuration(): CatalogSyndicationDestinationConfiguration
    {
        return $this->configuration;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationRegisterRequest', false)) {
    class_alias(CatalogSyndicationDestinationRegisterRequest::class, __NAMESPACE__.'\\SyndicationDestinationRegisterRequest');
}
