<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventInterface;
/**
 * Defines the contract for category syndication recovery candidate prepared.
 */
interface CategorySyndicationRecoveryCandidatePreparedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable;
}
