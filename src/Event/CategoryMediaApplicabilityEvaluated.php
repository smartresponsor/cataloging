<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;

/**
 * Represents the category media applicability evaluated application event.
 */
final readonly class CategoryMediaApplicabilityEvaluated implements CategoryMediaApplicabilityEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private string $categoryId,
        private string $channel,
        private string $locale,
        private array $requiredMissing,
        private array $warnings,
        private array $checks,
        private array $matchedBindingIds,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $evaluatedAt,
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
