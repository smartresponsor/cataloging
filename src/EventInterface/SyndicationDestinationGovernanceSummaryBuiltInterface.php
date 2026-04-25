<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface;

/**
 * Defines the contract for category syndication destination governance summary built.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogSyndicationDestinationGovernanceSummaryBuiltInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryBuiltInterface', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummaryBuiltInterface::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryBuiltInterface');
}
