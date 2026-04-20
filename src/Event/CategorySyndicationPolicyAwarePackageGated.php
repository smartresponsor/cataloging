<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategorySyndicationPolicyAwarePackageGatedInterface;

/**
 * Represents the category syndication policy aware package gated application event.
 */
final readonly class CategorySyndicationPolicyAwarePackageGated implements CategorySyndicationPolicyAwarePackageGatedInterface
{
    /** @param array<string,mixed> $payload */
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
