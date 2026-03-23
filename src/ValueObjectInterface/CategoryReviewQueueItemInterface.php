<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategoryReviewQueueItemInterface
{
    public function requestId(): string;

    public function categoryId(): string;

    public function assignedReviewer(): string;

    public function priority(): string;

    public function requestState(): string;

    public function readyForReview(): bool;

    /** @return list<string> */
    public function readinessWarnings(): array;

    public function dueAt(): ?\DateTimeImmutable;
}
