<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryChangeRequestAssignedInterface;

final class CategoryChangeRequestAssigned implements CategoryChangeRequestAssignedInterface
{
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $assignedReviewer,
        private readonly string $assignedBy,
        private readonly string $priority,
        private readonly \DateTimeImmutable $assignedAt,
        private readonly ?\DateTimeImmutable $dueAt,
    ) {
    }

    public function payload(): array
    {
        return [
            'requestId' => $this->requestId,
            'categoryId' => $this->categoryId,
            'assignedReviewer' => $this->assignedReviewer,
            'assignedBy' => $this->assignedBy,
            'priority' => $this->priority,
            'assignedAt' => $this->assignedAt->format(DATE_ATOM),
            'dueAt' => $this->dueAt?->format(DATE_ATOM),
        ];
    }
}
