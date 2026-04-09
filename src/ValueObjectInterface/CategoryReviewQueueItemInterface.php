<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category review queue item.
 */
interface CategoryReviewQueueItemInterface
{
    /**
     * Handles the request id workflow.
     */
    public function requestId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the assigned reviewer workflow.
     */
    public function assignedReviewer(): string;

    /**
     * Handles the priority workflow.
     */
    public function priority(): string;

    /**
     * Handles the request state workflow.
     */
    public function requestState(): string;

    /**
     * Handles the ready for review workflow.
     */
    public function readyForReview(): bool;

    /** @return list<string> */
    public function readinessWarnings(): array;

    /**
     * Handles the due at workflow.
     */
    public function dueAt(): ?\DateTimeImmutable;
}
