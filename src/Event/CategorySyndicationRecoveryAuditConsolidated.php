<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;

/**
 * Represents the category syndication recovery audit consolidated application event.
 */
final readonly class CategorySyndicationRecoveryAuditConsolidated implements CategorySyndicationRecoveryAuditConsolidatedInterface
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
