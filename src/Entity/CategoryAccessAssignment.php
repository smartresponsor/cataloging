<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\CategoryAccessAssignmentInterface;
use Random\RandomException;

/**
 * Represents the category access assignment domain record.
 */
final class CategoryAccessAssignment implements CategoryAccessAssignmentInterface
{
    /**
     * Initializes the category access assignment service collaborators.
     */
    public function __construct(
        private readonly string $assignmentId,
        private readonly string $categoryId,
        private readonly string $actorUserId,
        private string $role,
        private string $status,
        private bool $isPrimary,
        private readonly \DateTimeImmutable $grantedAt,
        private ?\DateTimeImmutable $revokedAt,
    ) {
    }

    /**
     * @throws RandomException
     */
    public static function create(
        string $categoryId,
        string $actorUserId,
        string $role,
        bool $isPrimary = false,
    ): self {
        return new self(
            assignmentId: bin2hex(random_bytes(16)),
            categoryId: trim($categoryId),
            actorUserId: trim($actorUserId),
            role: trim($role),
            status: 'active',
            isPrimary: $isPrimary,
            grantedAt: new \DateTimeImmutable('now'),
            revokedAt: null,
        );
    }

    /**
     * Handles the assignment id workflow.
     */
    public function assignmentId(): string
    {
        return $this->assignmentId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the actor user id workflow.
     */
    public function actorUserId(): string
    {
        return $this->actorUserId;
    }

    /**
     * Handles the role workflow.
     */
    public function role(): string
    {
        return $this->role;
    }

    /**
     * Handles the status workflow.
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Determines whether the primary condition is satisfied.
     */
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /**
     * Handles the granted at workflow.
     */
    public function grantedAt(): \DateTimeImmutable
    {
        return $this->grantedAt;
    }

    /**
     * Handles the revoked at workflow.
     */
    public function revokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    /**
     * Handles the activate workflow.
     */
    public function activate(): void
    {
        $this->status = 'active';
        $this->revokedAt = null;
    }

    /**
     * Handles the revoke workflow.
     */
    public function revoke(): void
    {
        $this->status = 'revoked';
        $this->isPrimary = false;
        $this->revokedAt = new \DateTimeImmutable('now');
    }

    /**
     * Marks the primary state for the current workflow.
     */
    public function markPrimary(): void
    {
        $this->isPrimary = true;
    }

    /**
     * Handles the clear primary workflow.
     */
    public function clearPrimary(): void
    {
        $this->isPrimary = false;
    }

    /**
     * Handles the change role workflow.
     */
    public function changeRole(string $role): void
    {
        $this->role = trim($role);
    }
}
