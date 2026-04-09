<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface;

/**
 * Represents the category destination media readiness evaluated application event.
 */
final readonly class CategoryDestinationMediaReadinessEvaluated implements CategoryDestinationMediaReadinessEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private string $destinationId,
        private string $categoryId,
        private string $channel,
        private string $locale,
        private bool $publishable,
        private array $requiredMissing,
        private array $warnings,
        private array $checks,
        private array $matchedBindingIds,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $evaluatedAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return [
            'destinationId' => $this->destinationId,
            'categoryId' => $this->categoryId,
            'channel' => $this->channel,
            'locale' => $this->locale,
            'publishable' => $this->publishable,
            'requiredMissing' => $this->requiredMissing,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'matchedBindingIds' => $this->matchedBindingIds,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'evaluatedAt' => $this->evaluatedAt->format(DATE_ATOM),
        ];
    }
}
