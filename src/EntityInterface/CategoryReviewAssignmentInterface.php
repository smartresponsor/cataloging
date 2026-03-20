<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

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
