<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for syndication package build and gate workflows.
 */
final readonly class CategorySyndicationPackageBuildRequest
{
    /**
     * @param array<string,mixed>  $categoryData
     * @param array<string,string> $fieldMap
     * @param list<string>         $requiredFields
     */
    public function __construct(
        private CategorySyndicationPackageContext $context,
        private array $categoryData,
        private array $fieldMap,
        private array $requiredFields,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function context(): CategorySyndicationPackageContext
    {
        return $this->context;
    }

    /** @return array<string,mixed> */
    public function categoryData(): array
    {
        return $this->categoryData;
    }

    /** @return array<string,string> */
    public function fieldMap(): array
    {
        return $this->fieldMap;
    }

    /** @return list<string> */
    public function requiredFields(): array
    {
        return $this->requiredFields;
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }
}
