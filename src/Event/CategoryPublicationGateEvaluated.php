<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryPublicationGateEvaluatedInterface;

/**
 * Represents the category publication gate evaluated application event.
 */
final readonly class CategoryPublicationGateEvaluated implements CategoryPublicationGateEvaluatedInterface
{
    /**
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $categoryId,
        private string $workflowState,
        private bool $publishable,
        private array $blockers,
        private array $warnings,
        private array $checks,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'workflowState' => $this->workflowState,
            'publishable' => $this->publishable,
            'blockers' => $this->blockers,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}
