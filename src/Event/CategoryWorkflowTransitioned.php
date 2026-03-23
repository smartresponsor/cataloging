<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryWorkflowTransitionedInterface;

final class CategoryWorkflowTransitioned implements CategoryWorkflowTransitionedInterface
{
    public function __construct(
        private readonly string $categoryId,
        private readonly string $fromState,
        private readonly string $toState,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $occurredAt,
    ) {
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
