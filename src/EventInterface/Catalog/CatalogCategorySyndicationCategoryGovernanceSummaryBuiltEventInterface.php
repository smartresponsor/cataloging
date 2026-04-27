<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface\Catalog;

/**
 * Defines the contract for category syndication category governance summary built.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEventInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
