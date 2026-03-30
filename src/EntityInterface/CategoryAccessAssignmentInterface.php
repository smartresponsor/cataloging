<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface CategoryAccessAssignmentInterface
{
    public function assignmentId(): string;

    public function categoryId(): string;

    public function actorUserId(): string;

    public function role(): string;

    public function status(): string;

    public function isPrimary(): bool;

    public function grantedAt(): \DateTimeImmutable;

    public function revokedAt(): ?\DateTimeImmutable;
}
