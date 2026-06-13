<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface\Catalog;

/**
 * Defines the contract for category taxonomy.
 */
interface CatalogTaxonomyEntityInterface
{
    /**
     * Handles the id workflow.
     */
    public function id(): string;

    /**
     * Handles the code workflow.
     */
    public function code(): string;

    /** @return array<string,string> */
    public function nameEntity(): array;

    /** @return array<string,mixed> */
    public function rule(): array;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;

    /**
     * Handles the updated at workflow.
     */
    public function updatedAt(): \DateTimeImmutable;
}
