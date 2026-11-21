<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryChangeRequestAssignedEventInterface;

/**
 * Represents the category change request assigned application event.
 */
final readonly class CatalogCategoryChangeRequestAssignedEvent implements CatalogCategoryChangeRequestAssignedEventInterface
{
    /**
     * Initializes the category change request assigned service collaborators.
     */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $assignedReviewer,
        private string $assignedBy,
        private string $priority,
        private \DateTimeImmutable $assignedAt,
        private ?\DateTimeImmutable $dueAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
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
