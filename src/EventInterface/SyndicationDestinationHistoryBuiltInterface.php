<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface;

/**
 * Defines the contract for category syndication destination history built.
 */
interface CatalogSyndicationDestinationHistoryBuiltInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationHistoryBuiltInterface', false)) {
    class_alias(CatalogSyndicationDestinationHistoryBuiltInterface::class, __NAMESPACE__.'\\SyndicationDestinationHistoryBuiltInterface');
}
