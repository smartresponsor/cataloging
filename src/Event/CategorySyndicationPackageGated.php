<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategorySyndicationPackageGatedInterface;

/**
 * Represents the category syndication package gated application event.
 */
final class CategorySyndicationPackageGated implements CategorySyndicationPackageGatedInterface
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        private readonly array $payload,
        private readonly \DateTimeImmutable $occurredAt,
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
