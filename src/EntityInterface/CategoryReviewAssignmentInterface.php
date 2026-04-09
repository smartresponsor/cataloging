<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;
/**
 * Defines the contract for category review assignment.
 */
interface CategoryReviewAssignmentInterface
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
     * Handles the assigned by workflow.
     */
    public function assignedBy(): string;
    /**
     * Handles the priority workflow.
     */
    public function priority(): string;
    /**
     * Handles the assigned at workflow.
     */
    public function assignedAt(): \DateTimeImmutable;
    /**
     * Handles the due at workflow.
     */
    public function dueAt(): ?\DateTimeImmutable;
}
