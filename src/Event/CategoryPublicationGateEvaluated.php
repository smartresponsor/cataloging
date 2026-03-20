<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Event;

use App\EventInterface\CategoryPublicationGateEvaluatedInterface;

final class CategoryPublicationGateEvaluated implements CategoryPublicationGateEvaluatedInterface
{
    /**
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly string $categoryId,
        private readonly string $workflowState,
        private readonly bool $publishable,
        private readonly array $blockers,
        private readonly array $warnings,
        private readonly array $checks,
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
