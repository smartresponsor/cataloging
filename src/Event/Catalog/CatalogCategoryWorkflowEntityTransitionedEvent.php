<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryWorkflowEntityTransitionedEventInterface;

/**
 * Represents the category workflow transitioned application event.
 */
final readonly class CatalogCategoryWorkflowEntityTransitionedEvent implements CatalogCategoryWorkflowEntityTransitionedEventInterface
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
if (!class_exists(__NAMESPACE__.'\\CategoryWorkflowTransitioned', false)) {
    class_alias(CatalogCategoryWorkflowEntityTransitionedEvent::class, __NAMESPACE__.'\\CategoryWorkflowTransitioned');
}
