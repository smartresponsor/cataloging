<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;
/**
 * Represents the category media applicability evaluated application event.
 */
final class CategoryMediaApplicabilityEvaluated implements CategoryMediaApplicabilityEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private readonly string $categoryId,
        private readonly string $channel,
        private readonly string $locale,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $matchedBindingIds,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $evaluatedAt,
    ) {
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'channel' => $this->channel,
            'locale' => $this->locale,
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
