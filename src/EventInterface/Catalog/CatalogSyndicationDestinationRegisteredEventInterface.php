<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface\Catalog;

/**
 * Defines the contract for category syndication destination registered.
 */
interface CatalogSyndicationDestinationRegisteredEventInterface
{
    /**
     * @return array<string,mixed>
     */
    public function payload(): array;

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationRegisteredInterface', false)) {
    class_alias(CatalogSyndicationDestinationRegisteredEventInterface::class, __NAMESPACE__.'\\SyndicationDestinationRegisteredInterface');
}
