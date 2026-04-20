<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;

/**
 * Represents the category workflow transitioned application event.
 */
final readonly class CategoryWorkflowTransitioned implements CategoryWorkflowTransitionedInterface
{
    /**
     * Initializes the category workflow transitioned service collaborators.
     */
    public function __construct(
        private string $categoryId,
        private string $fromState,
        private string $toState,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * Handles the event name workflow.
     */
    public function eventName(): string
    {
        return 'category.workflow_transitioned';
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'fromState' => $this->fromState,
            'toState' => $this->toState,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}
