<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventInterface;
/**
 * Defines the contract for category syndication fallback aware package gated.
 */
interface CategorySyndicationFallbackAwarePackageGatedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
