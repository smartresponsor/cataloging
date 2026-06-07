<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category review assignment workflows.
 */
final readonly class CatalogCategoryReviewAssignmentEntityRequest
{
    public function __construct(
        private string $requestId,
        private string $assignedReviewer,
        private string $assignedBy,
        private string $priority = 'normal',
        private ?\DateTimeImmutable $dueAt = null,
    ) {
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function assignedReviewer(): string
    {
        return $this->assignedReviewer;
    }

    public function assignedBy(): string
    {
        return $this->assignedBy;
    }

    public function priority(): string
    {
        return $this->priority;
    }

    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryReviewAssignmentRequest', false)) {
    class_alias(CatalogCategoryReviewAssignmentEntityRequest::class, __NAMESPACE__.'\\CategoryReviewAssignmentRequest');
}
