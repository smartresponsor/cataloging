<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CatalogSyndicationDestinationRegisteredInterface;

/**
 * Represents the category syndication destination registered application event.
 */
final readonly class CatalogSyndicationDestinationRegistered implements CatalogSyndicationDestinationRegisteredInterface
{
    /**
     * @param array<string,mixed> $payload
     */
    public function __construct(
        private array $payload,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationRegistered', false)) {
    class_alias(CatalogSyndicationDestinationRegistered::class, __NAMESPACE__.'\\SyndicationDestinationRegistered');
}
