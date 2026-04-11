<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for syndication destination registration workflows.
 */
final readonly class CategorySyndicationDestinationRegisterRequest
{
    public function __construct(
        private CategorySyndicationDestinationDefinition $definition,
        private CategorySyndicationDestinationConfiguration $configuration,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function definition(): CategorySyndicationDestinationDefinition
    {
        return $this->definition;
    }

    public function configuration(): CategorySyndicationDestinationConfiguration
    {
        return $this->configuration;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
