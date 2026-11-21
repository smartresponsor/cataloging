<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category publication gate assertion workflows.
 */
final readonly class CategoryPublicationGateAssertionRequest
{
    /** @param array<string,bool> $checks */
    public function __construct(
        private string $workflowState,
        private array $checks,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function workflowState(): string
    {
        return $this->workflowState;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
