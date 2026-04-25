<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

/**
 * Defines the contract for category access assignment.
 */
interface CatalogCategoryAccessAssignmentEntityInterface
{
    /**
     * Handles the assignment id workflow.
     */
    public function assignmentId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the actor user id workflow.
     */
    public function actorUserId(): string;

    /**
     * Handles the role workflow.
     */
    public function role(): string;

    /**
     * Handles the status workflow.
     */
    public function status(): string;

    /**
     * Determines whether the primary condition is satisfied.
     */
    public function isPrimary(): bool;

    /**
     * Handles the granted at workflow.
     */
    public function grantedAt(): \DateTimeImmutable;

    /**
     * Handles the revoked at workflow.
     */
    public function revokedAt(): ?\DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\CategoryAccessAssignmentInterface', false)) {
    class_alias(CatalogCategoryAccessAssignmentEntityInterface::class, __NAMESPACE__.'\\CategoryAccessAssignmentInterface');
}
