<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryAccessAssignmentInterface;

final class CategoryAccessAssignment implements CategoryAccessAssignmentInterface
{
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

    public function assignmentId(): string
    {
        return $this->assignmentId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function actorUserId(): string
    {
        return $this->actorUserId;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function grantedAt(): \DateTimeImmutable
    {
        return $this->grantedAt;
    }

    public function revokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function activate(): void
    {
        $this->status = 'active';
        $this->revokedAt = null;
    }

    public function revoke(): void
    {
        $this->status = 'revoked';
        $this->isPrimary = false;
        $this->revokedAt = new \DateTimeImmutable('now');
    }

    public function markPrimary(): void
    {
        $this->isPrimary = true;
    }

    public function clearPrimary(): void
    {
        $this->isPrimary = false;
    }

    public function changeRole(string $role): void
    {
        $this->role = trim($role);
    }
}
