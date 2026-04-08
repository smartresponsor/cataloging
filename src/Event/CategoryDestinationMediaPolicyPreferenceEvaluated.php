<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryDestinationMediaPolicyPreferenceEvaluatedInterface;
/**
 * Represents the category destination media policy preference evaluated application event.
 */
final class CategoryDestinationMediaPolicyPreferenceEvaluated implements CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        private readonly array $payload,
        private readonly \DateTimeImmutable $occurredAt,
    ) {
    }
    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return $this->payload;
    }
    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
