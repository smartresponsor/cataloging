<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface CategoryReviewAssignmentInterface
{
    public function requestId(): string;

    public function categoryId(): string;

    public function assignedReviewer(): string;

    public function assignedBy(): string;

    public function priority(): string;

    public function assignedAt(): \DateTimeImmutable;

    public function dueAt(): ?\DateTimeImmutable;
}
